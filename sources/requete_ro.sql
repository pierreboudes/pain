select pain_sformation.numero, pain_tranche.cm, pain_tranche.td, 
pain_cours.nom_cours, pain_cours.semestre, pain_formation.nom, 
pain_formation.annee_etude, pain_formation.parfum, pain_enseignant.nom 
from pain_tranche, pain_cours, pain_formation, pain_sformation, pain_enseignant 
where 
pain_cours.id_enseignant = pain_enseignant.id_enseignant 
and pain_tranche.id_cours = pain_cours.id_cours 
and pain_cours.id_formation = pain_formation.id_formation 
and pain_formation.id_sformation = pain_sformation.id_sformation 
and annee_universitaire = 2012; 
