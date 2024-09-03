from flask import Flask, request, jsonify
from flask_cors import CORS

app = Flask(__name__)
CORS(app)

def read_titles_from_file(file_path):
    try:
        with open(file_path, 'r') as file:
            titles = [line.strip() for line in file if line.strip()]
        return titles
    except FileNotFoundError:
        return None

def read_labels_from_file(file_path):
    try:
        with open(file_path, 'r') as file:
            labels = [line.strip().lower() == 'true' for line in file if line.strip()]
        return labels
    except FileNotFoundError:
        return None

@app.route('/detect', methods=['POST'])
def detect():
    if request.content_type != 'application/json':
        return jsonify({'error': 'Tip de media neacceptat!'}), 415

    data = request.get_json()
    title = data.get('title', '')

    if not title:
        return jsonify({'error': 'Titlul lipsește!'}), 400

    titles_file_path = 'stiri.txt'  # Fișierul cu titluri
    labels_file_path = 'rezultat.txt'  # Fișierul cu etichete

    titles = read_titles_from_file(titles_file_path)
    labels = read_labels_from_file(labels_file_path)

    if titles is None:
        return jsonify({'error': 'Fișierul cu titluri nu a fost găsit!'}), 404

    if labels is None:
        return jsonify({'error': 'Fișierul cu etichete nu a fost găsit!'}), 404

    if len(titles) != len(labels):
        return jsonify({'error': 'Numărul de titluri nu corespunde cu numărul de etichete!'}), 400

    if title in titles:
        index = titles.index(title)
        label = labels[index]
        message = "Știrea este falsă!" if not label else "Știrea este adevărată!"

        return jsonify({
            'title': title,
            'is_fake': not label,
            'message': message
        })
    else:
        return jsonify({'error': 'Titlul nu a fost găsit în fișier!'}), 404

if __name__ == '__main__':
    app.run(port=5000)
