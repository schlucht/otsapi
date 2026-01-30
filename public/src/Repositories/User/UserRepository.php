<?php

declare(strict_types=1);

namespace Ots\OTS\Repositories\User;

use Ots\OTS\Repositories\Repository;
use Ots\OTS\Database;
use Ots\OTS\Models\User\UserModel;

class UserRepository extends Repository
{
    public function __construct(private Database $database)
    {
        parent::__construct($database);
        $this->table = "users";
    }

    public function getUsers(): array
    {
        try {
            $this->pdo = $this->database->getConnection();
            $sql = "SELECT user_id, firstname, lastname, email, created_at, updated_at FROM $this->table";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $users = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $user = new UserModel();
                $user->id = (int)$row['user_id'];
                $user->firstname = $row['firstname'];
                $user->lastname = $row['lastname'];
                $user->email = $row['email'];
                $user->created = new \DateTime($row['created_at']);
                $user->updated = new \DateTime($row['updated_at']);
                $users[] = $user;
            }
            return $users;
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    public function createUser(UserModel $user): int
    {
        try {
            $this->pdo = $this->database->getConnection();
            $passwordHash = password_hash($user->password, PASSWORD_BCRYPT);
            $sql = "INSERT INTO $this->table (firstname, lastname, email, password, created_at) 
                VALUES (:firstname, :lastname, :email, :password, NOW())";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':firstname', $user->firstname);
            $stmt->bindValue(':lastname', $user->lastname);
            $stmt->bindValue(':email', $user->email);
            $stmt->bindValue(':password', $passwordHash);
            $stmt->execute();
            return (int)$this->pdo->lastInsertId();
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    public function findByEmail(string $email): ?UserModel
    {
        try {
            $this->pdo = $this->database->getConnection();
            $sql = "SELECT user_id, firstname, lastname, email, password FROM $this->table WHERE email = :email";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->execute();
            $res = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($res) {
                $user = new UserModel();
                $user->id = (int)$res['user_id'];
                $user->firstname = $res['firstname'];
                $user->lastname = $res['lastname'];
                $user->email = $res['email'];
                $user->password = $res['password'];
                return $user;
            }
            return null;
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    public function findById(int $id): ?UserModel
    {
        try {
            $this->pdo = $this->database->getConnection();
            $sql = "SELECT user_id, firstname, lastname, email FROM $this->table WHERE user_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();
            $res = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($res) {
                $user = new UserModel();
                $user->id = (int)$res['user_id'];
                $user->firstname = $res['firstname'];
                $user->lastname = $res['lastname'];
                $user->email = $res['email'];
                return $user;
            }
            return null;
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    public function delete(int $id): bool
    {
        try {            
            $this->pdo = $this->database->getConnection();
            $sql = "DELETE FROM $this->table WHERE user_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
            $stmt->execute();            
            return $stmt->rowCount() > 0;
        } catch (\PDOException $e) {
            throw $e;
        }
    }

    public function update(UserModel $user): ?UserModel {
        try {            
            $sql = "UPDATE $this->table SET firstname=:firstname, lastname=:lastname, mail=:email, updated_at=NOW() WHERE user_id = :id";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $user->id);
            $stmt->bindValue(':firstname', $user->firstname);
            $stmt->bindValue(':lastname', $user->lastname);
            $stmt->bindValue(':email', $user->email);
            $stmt->execute();
            $usr = $this->findById($user->id);
            return $usr;
        } catch (\PDOException $e) {
            throw $e;
        }
    }
}
