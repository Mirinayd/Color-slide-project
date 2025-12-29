# Importation de la BDD dans phpMyAdmin

Pour permettre l'importation de la BDD (contenant des fichiers volumineux), il est nécessaire d'ouvrir la console de requêtes SQL puis d'exécuter la ligne suivante :

```sql
SET GLOBAL max_allowed_packet=1073741824;
```
