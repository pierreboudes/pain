insert into pain_tranche 
	(id_enseignant, id_cours, tp, htd, groupe)
	SELECT 3, pain_tranche.id_cours, pain_tranche.tp, pain_tranche.tp, groupe+10 FROM  pain_tranche,pain_cours,pain_formation,pain_sformation
	WHERE pain_tranche.id_cours = pain_cours.id_cours 
	AND pain_cours.id_formation = pain_formation.id_formation
	AND annee_universitaire=2013
	AND pain_formation.id_sformation = pain_sformation.id_sformation
	AND pain_tranche.tp>0
	AND (pain_sformation.nom="DUT INFO FI" OR pain_sformation.nom="DUT INFO APP");



