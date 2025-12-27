import random
from faker import Faker

fake = Faker('id_ID')  
num_records = 100
file_name = 'inventaris_lab_100.sql'

# Kategori barang laboratorium
kategori_barang = [
    'Komputer', 'Elektronik', 'Jaringan',
    'Laboratorium', 'Perabot', 'Alat Tulis'
]

# Kondisi barang
kondisi_barang = [
    'Baik', 'Rusak', 'Perlu Perbaikan'
]

# Nama barang umum di lab
nama_barang_list = [
    'Komputer', 'Monitor', 'Printer',
    'Router', 'Switch', 'Proyektor',
    'Keyboard', 'Mouse', 'Meja', 'Kursi'
]

with open(file_name, 'w', encoding='utf-8') as f:
    f.write("USE inventaris_lab_db;\n\n")

    f.write("INSERT INTO inventaris (nama_barang, kategori, kondisi, jumlah) VALUES\n")

    for i in range(1, num_records + 1):
        nama_barang = (
            random.choice(nama_barang_list)
            + " "
            + fake.word().capitalize()
        ).replace("'", "''")

        kategori = random.choice(kategori_barang)
        kondisi = random.choice(kondisi_barang)
        jumlah = random.randint(0, 50)

        line = f"('{nama_barang}', '{kategori}', '{kondisi}', {jumlah})"

        if i < num_records:
            line += ",\n"
        else:
            line += ";\n"

        f.write(line)

print(f"File '{file_name}' berhasil dibuat dengan {num_records} data inventaris.")
