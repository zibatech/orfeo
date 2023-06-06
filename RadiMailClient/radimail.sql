create table radimail (
	id serial PRIMARY KEY,
	msgno integer,
	asunto text,
	desde varchar(1000),
	para varchar(1000),
	fecha text,	
	uniqueid integer unique,
	radi_nume_radi text
);
