create table radimail_adjunto (
	id serial PRIMARY KEY,
	radimail_id integer,
	name text, 
	filename text,
	partnum integer, 
	enc integer, 
	type text,
	FOREIGN KEY (radimail_id) REFERENCES radimail (uniqueid)
);
