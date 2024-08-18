from flask import Flask, request, jsonify
import mysql.connector
import math

app = Flask(__name__)

def get_db_connection():
    conn = mysql.connector.connect(
        host='localhost',
        user='root',
        password='prabin@123', 
        database='blood'
    )
    return conn

def haversine(lat1, lon1, lat2, lon2):
    earth_radius = 6371 
    dLat = math.radians(lat2 - lat1)
    dLon = math.radians(lon2 - lon1)
    a = math.sin(dLat / 2) ** 2 + math.cos(math.radians(lat1)) * math.cos(math.radians(lat2)) * math.sin(dLon / 2) ** 2
    c = 2 * math.atan2(math.sqrt(a), math.sqrt(1 - a))
    return earth_radius * c

def get_compatible_blood_types(blood_type):
    compatibility = {
        'Apos': ['Apos', 'Aneg', 'Opos', 'Oneg'],
        'Aneg': ['Aneg', 'Oneg'],
        'Bpos': ['Bpos', 'Bneg', 'Opos', 'Oneg'],
        'Bneg': ['Bneg', 'Oneg'],
        'ABpos': ['Apos', 'Aneg', 'Bpos', 'Bneg', 'ABpos', 'ABneg', 'Opos', 'Oneg'],
        'ABneg': ['Aneg', 'Bneg', 'ABneg', 'Oneg'],
        'Opos': ['Opos', 'Oneg'],
        'Oneg': ['Oneg']
    }
    return compatibility.get(blood_type, [])

def calculate_knn_score(user_blood_type, donor_blood_type, distance):
    compatible_types = get_compatible_blood_types(user_blood_type)
    if donor_blood_type in compatible_types:
        score = 1
    elif user_blood_type == donor_blood_type:
        score = 0.5
    else:
        score = 0
    
    # Adjust score based on distance: closer donors get higher scores
    distance_factor = 1 / (1 + distance)  # Inverse distance weighting
    return score * distance_factor

@app.route('/search_donors', methods=['POST'])
def search_donors():
    data = request.get_json()
    blood_type = data.get('blood_type')
    user_lat = data.get('user_lat')
    user_lng = data.get('user_lng')
    k = data.get('k', 5)  # Number of nearest donors to return

    conn = get_db_connection()
    cursor = conn.cursor(dictionary=True)

    query = """
        SELECT id, fullname, blood_type, age, weight, latitude, longitude
        FROM users
        WHERE role = 'donor'
          AND id NOT IN (
              SELECT donor_id
              FROM requests
              WHERE status = 'Accepted'
                AND accepted_date > DATE_SUB(NOW(), INTERVAL 1 MONTH)
          )
    """
    cursor.execute(query)
    donors = cursor.fetchall()

    # Calculate distances and filter only compatible donors within 10 km
    compatible_donors = []
    for donor in donors:
        if donor['blood_type'] in get_compatible_blood_types(blood_type):  # Only consider compatible donors
            distance = haversine(user_lat, user_lng, donor['latitude'], donor['longitude'])
            if distance <= 10:  # Only include donors within 10 km radius
                donor['distance'] = distance
                donor['knn_score'] = calculate_knn_score(blood_type, donor['blood_type'], distance)
                compatible_donors.append(donor)

    # Sort compatible donors first by KNN score (descending) and then by distance (ascending)
    compatible_donors = sorted(compatible_donors, key=lambda x: (-x['knn_score'], x['distance']))

    # Return top k compatible donors
    top_donors = compatible_donors[:k]

    cursor.close()
    conn.close()

    return jsonify(top_donors)

@app.route('/send_blood_request', methods=['POST'])
def send_blood_request():
    data = request.get_json()
    user_id = data.get('user_id')
    donor_id = data.get('donor_id')
    blood_type = data.get('blood_type')
    latitude = data.get('latitude')
    longitude = data.get('longitude')

    conn = get_db_connection()
    cursor = conn.cursor()

    cursor.execute("SELECT 1 FROM requests WHERE user_id = %s AND donor_id = %s AND status = 'Pending'", (user_id, donor_id))
    if cursor.fetchone():
        cursor.close()
        conn.close()
        return jsonify({'success': False, 'message': 'Request already pending'})

    cursor.execute("INSERT INTO requests (user_id, donor_id, blood_type, latitude, longitude, status) VALUES (%s, %s, %s, %s, %s, 'Pending')",
                   (user_id, donor_id, blood_type, latitude, longitude))
    conn.commit()

    cursor.close()
    conn.close()

    return jsonify({'success': True, 'message': 'Request sent successfully'})

if __name__ == '__main__':
    app.run(debug=True)
