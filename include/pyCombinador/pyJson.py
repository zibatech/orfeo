records = []
records.append({})
records[0]["TERCERO_NOMBRE"] = "JAIRO LOPRUEBAS"
records[0]["TERCERO_DOCUMENTO"] = "198012120"
records[0]["TERCERO_PUNTAJE"] = "79790222"
records.append({})
records[1]["TERCERO_NOMBRE"] = "juanito"
records[1]["TERCERO_DOCUMENTO"] = "0001"
records[1]["TERCERO_PUNTAJE"] = "0001"
records.append({})
records[2]["TERCERO_NOMBRE"] = "ANA C"
records[2]["TERCERO_DOCUMENTO"] = "787854212"
records[2]["TERCERO_PUNTAJE"] = "60"

#with open('data.json') as file:
#    data = json.load(file)
with open('data.json', 'w') as file:
    json.dump(data, file, indent=4)
