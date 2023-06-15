#def crop_image(image):
#    # Impor modul dlib dan cv2
#    import dlib
#    import cv2
#
#    # Muat gambar dan ubah ke grayscale
#    gray = cv2.cvtColor(image, cv2.COLOR_BGR2GRAY)
#
#    # Buat objek face_detector menggunakan dlib.get_frontal_face_detector()
#    face_detector = dlib.get_frontal_face_detector()
#
#    # Deteksi wajah di gambar grayscale menggunakan face_detector(image, 1) dan simpan hasilnya di variabel rects
#    rects = face_detector(gray, 1)
#
#    # Lakukan perulangan untuk setiap deteksi wajah dan dapatkan koordinat dari setiap persegi wajah menggunakan rect.left(), rect.top(), rect.right(), dan rect.bottom()
#    for i, rect in enumerate(rects):
#        # Hitung lebar dan tinggi persegi wajah
#        width = rect.right() - rect.left()
#        height = rect.bottom() - rect.top()
#
#        # Hitung koordinat baru dengan menambahkan padding sebesar 50%
#        left = max(0, rect.left() - width // 2)
#        top = max(0, rect.top() - height // 2)
#        right = min(image.shape[1], rect.right() + width // 2)
#        bottom = min(image.shape[0], rect.bottom() + height // 2)
#
#        # Potong gambar menggunakan koordinat baru dan simpan hasilnya menggunakan cv2.imwrite()
#        cropped_image = image[top:bottom, left:right]
#        
#        # Ubah ukuran gambar menjadi 100x100 piksel menggunakan cv2.resize()
#        #resized_image = cv2.resize(cropped_image, (100, 100))
#        # Simpan gambar yang telah diubah ukurannya dengan nama baru
#        #cv2.imwrite("tes.jpg", resized_image)
#    return cropped_image

def crop_image2(image):
    from retinaface import RetinaFace
    # Initialize RetinaFace
    detector = RetinaFace(quality="normal")

    # Detect faces in the image
    faces = detector.predict(image)

    # Iterate through the detected faces
    for face in faces:
        # Extract face bounding box coordinates
        x1, y1, x2, y2 = int(face['x1']), int(face['y1']), int(face['x2']), int(face['y2'])

        # Calculate the zoom-out factor
        zoom_out_factor = 0.1  # Adjust this value to control the amount of zoom-out

        # Calculate the new bounding box coordinates with zoom out
        new_x1 = max(0, x1 - int((x2 - x1) * zoom_out_factor))
        new_y1 = max(0, y1 - int((y2 - y1) * zoom_out_factor))
        new_x2 = min(image.shape[1], x2 + int((x2 - x1) * zoom_out_factor))
        new_y2 = min(image.shape[0], y2 + int((y2 - y1) * zoom_out_factor))

        # Crop the face region from the image
        cropped_face = image[new_y1:new_y2, new_x1:new_x2]

        return cropped_face