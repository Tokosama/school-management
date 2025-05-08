<?php
class EnseignantController
{
    private $pdo;

    public function __construct($dbHost, $dbName, $dbUser, $dbPass)
    {
        try {
            $this->pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8", $dbUser, $dbPass);
        } catch (PDOException $e) {
            die("Erreur de connexion à la base de données : " . $e->getMessage());
        }
    }

    // Lister tous les enseignants
    public function index()
    {
        $stmt = $this->pdo->query("SELECT * FROM enseignants");
        $enseignants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $enseignants;
    }

    // Ajouter un nouvel enseignant
    public function store($data)
    {
        $sql = "INSERT INTO enseignants (nom, prenom, email, specialite) VALUES (:nom, :prenom, :email, :specialite)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':email' => $data['email'],
            ':specialite' => $data['specialite']
        ]);
    }

    // Afficher un enseignant par ID
    public function show($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM enseignants WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Mettre à jour un enseignant
    public function update($id, $data)
    {
        $sql = "UPDATE enseignants SET nom = :nom, prenom = :prenom, email = :email, specialite = :specialite WHERE id = :id";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            ':nom' => $data['nom'],
            ':prenom' => $data['prenom'],
            ':email' => $data['email'],
            ':specialite' => $data['specialite'],
            ':id' => $id
        ]);
    }

    // Supprimer un enseignant
    public function destroy($id)
    {
        $stmt = $this->pdo->prepare("DELETE FROM enseignants WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
?>
