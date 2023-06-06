<?
$usua_doc=$_SESSION["usua_doc"];
$isql=<<<EOF
select 
  e.sgd_exp_numero as "Num Exp"
, (case 
  when e.sgd_cerrado = 1 then 'Cerrado'
  else 'Abierto' end) as "Estado"
, e.sgd_sexp_parexp1 as "Nombre Exp"
, u.usua_login as "Admin Exp"
, e.sgd_sexp_fechafin as "Fecha de cierre"
--, er.sgd_exp_archivo
, to_char(min(er.sgd_exp_fech),'DD/MM/YYYY') as "Fecha extrema inicial"
, to_char(max(er.sgd_exp_fech),'DD/MM/YYYY') as "Fecha extrema final"
, e.sgd_exp_numero as "CHK_CHKANULAR"

--select *
from sgd_sexp_secexpedientes e
left join usuario u on (u.usua_doc=e.usua_doc_responsable)
left join sgd_exp_expediente er on (e.sgd_exp_numero=er.sgd_exp_numero)
where e.depe_codi='$dependencia'
and (er.sgd_exp_archivo is null or er.sgd_exp_archivo<>2)
$filtroExp
group by 1,2,3,4,5
--group by 1,2,3,4
EOF;
//echo "<pre>$isql</pre>";
?>
