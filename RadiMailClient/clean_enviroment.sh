rm /tmp/radimail/ -rf
mkdir /tmp/radimail/
echo "Bodega Limpia"
psql -h localhost -U orfeo_user -d orfeo_db -f updateDB.sql

