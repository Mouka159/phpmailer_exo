-- Ajouter les contraintes UNIQUE à la table utilisateur
-- Exécutez ce script SQL pour sécuriser l'unicité des données

-- Vérifier si la contrainte n'existe pas déjà pour email
ALTER TABLE utilisateur 
ADD CONSTRAINT unique_email UNIQUE (email);

-- Vérifier si la contrainte n'existe pas déjà pour nom
ALTER TABLE utilisateur 
ADD CONSTRAINT unique_nom UNIQUE (nom);

-- Note: La contrainte UNIQUE pour le mot de passe (mdp) n'est pas recommandée
-- car elle crée des problèmes de sécurité. La vérification PHP est suffisante.
-- Si vous voulez vraiment l'implémenter au niveau DB:
-- ALTER TABLE utilisateur ADD CONSTRAINT unique_mdp UNIQUE (mdp);

-- Vérifiez les contraintes :
SHOW KEYS FROM utilisateur WHERE Column_name IN ('email', 'nom');
