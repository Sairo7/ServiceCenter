<?php
/**
 * UserModel Class
 * Handles database operations for 15-table relational architecture
 */

class UserModel
{
    private $db;
    private $table = 'users';

    public function __construct()
    {
        $this->db = new Database();
    }

    public function getUserByUsername($username)
    {
        $input = strtolower(trim($username));
        $cleanPhone = preg_replace('/[^0-9]/', '', $input);

        $altUsername = $input;
        if ($input === 'customer' || $input === 'customer1') {
            $altUsername = ($input === 'customer') ? 'customer1' : 'customer';
        } elseif ($input === 'teknisi' || $input === 'teknisi1') {
            $altUsername = ($input === 'teknisi') ? 'teknisi1' : 'teknisi';
        } elseif ($input === 'kepalasekolah' || $input === 'kepala_sekolah') {
            $altUsername = ($input === 'kepalasekolah') ? 'kepala_sekolah' : 'kepalasekolah';
        }

        if ($this->db->isConnected()) {
            $sql = "SELECT u.*, r.name as role_name, r.display_name as role_display
                    FROM " . $this->table . " u
                    JOIN roles r ON u.role_id = r.id
                    WHERE LOWER(u.username) = :username 
                       OR LOWER(u.username) = :alt_username 
                       OR LOWER(u.email) = :email
                       OR u.phone = :phone 
                       OR REPLACE(REPLACE(REPLACE(u.phone, '-', ''), ' ', ''), '+', '') = :clean_phone
                    LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':username', $input);
            $this->db->bind(':alt_username', strtolower($altUsername));
            $this->db->bind(':email', $input);
            $this->db->bind(':phone', $input);
            $this->db->bind(':clean_phone', $cleanPhone ?: $input);
            $result = $this->db->single();
            if ($result) {
                // normalize role attribute to match string role name
                $result['role'] = $result['role_name'];
                $result['name'] = $result['full_name'];
                return $result;
            }
        }

        // Fallback demo users matching 1 Kepsek, 1 Operator, 3 Teknisi, 3 Customers
        $demoUsers = $this->getDemoUsers();
        foreach ($demoUsers as $user) {
            $uName = strtolower($user['username'] ?? '');
            $uEmail = strtolower($user['email'] ?? '');
            $uPhone = preg_replace('/[^0-9]/', '', $user['phone'] ?? '');

            if ($uName === $input || ($uEmail && $uEmail === $input) || $uName === strtolower($altUsername) || (!empty($cleanPhone) && $uPhone === $cleanPhone)) {
                return $user;
            }
        }

        return null;
    }

    public function getUserById($id)
    {
        $id = (int)$id;
        if ($this->db->isConnected()) {
            $sql = "SELECT u.*, r.name as role_name, r.display_name as role_display
                    FROM " . $this->table . " u
                    JOIN roles r ON u.role_id = r.id
                    WHERE u.id = :id";
            $this->db->query($sql);
            $this->db->bind(':id', $id);
            $result = $this->db->single();
            if ($result) {
                $result['role'] = $result['role_name'];
                $result['name'] = $result['full_name'];
                return $result;
            }
        }

        $demoUsers = $this->getDemoUsers();
        foreach ($demoUsers as $user) {
            if ((int)$user['id'] === $id) {
                return $user;
            }
        }

        return null;
    }

    public function isUsernameTakenByOther($username, $userId)
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT id FROM " . $this->table . " WHERE LOWER(username) = LOWER(:username) AND id != :id LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':username', trim($username));
            $this->db->bind(':id', (int)$userId);
            $res = $this->db->single();
            return !empty($res);
        }
        return false;
    }

    public function isEmailTakenByOther($email, $userId)
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT id FROM " . $this->table . " WHERE LOWER(email) = LOWER(:email) AND id != :id LIMIT 1";
            $this->db->query($sql);
            $this->db->bind(':email', trim($email));
            $this->db->bind(':id', (int)$userId);
            $res = $this->db->single();
            return !empty($res);
        }
        return false;
    }

    public function updateProfile($userId, $fullName, $email, $phone, $username = null)
    {
        if ($this->db->isConnected()) {
            if ($username !== null && trim($username) !== '') {
                $sql = "UPDATE " . $this->table . " SET full_name = :full_name, username = :username, email = :email, phone = :phone WHERE id = :id";
                $this->db->query($sql);
                $this->db->bind(':full_name', trim($fullName));
                $this->db->bind(':username', trim($username));
                $this->db->bind(':email', trim($email));
                $this->db->bind(':phone', trim($phone));
                $this->db->bind(':id', (int)$userId);
            } else {
                $sql = "UPDATE " . $this->table . " SET full_name = :full_name, email = :email, phone = :phone WHERE id = :id";
                $this->db->query($sql);
                $this->db->bind(':full_name', trim($fullName));
                $this->db->bind(':email', trim($email));
                $this->db->bind(':phone', trim($phone));
                $this->db->bind(':id', (int)$userId);
            }
            return $this->db->execute();
        }
        return false;
    }

    public function updateContact($userId, $email, $phone)
    {
        if ($this->db->isConnected()) {
            $sql = "UPDATE " . $this->table . " SET email = :email, phone = :phone WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':email', trim($email));
            $this->db->bind(':phone', trim($phone));
            $this->db->bind(':id', (int)$userId);
            return $this->db->execute();
        }
        return false;
    }

    /**
     * Update user password securely with BCRYPT hash
     */
    public function updatePassword($userId, $newPassword)
    {
        if ($this->db->isConnected()) {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $sql = "UPDATE " . $this->table . " SET password = :password WHERE id = :id";
            $this->db->query($sql);
            $this->db->bind(':password', $hashedPassword);
            $this->db->bind(':id', (int)$userId);
            return $this->db->execute();
        }
        return true; // Return true in demo/offline mode
    }

    public function authenticate($username, $password)
    {
        $user = $this->getUserByUsername($username);

        if ($user) {
            if (password_verify($password, $user['password'])) {
                return $user;
            }
        }
        return false;
    }

    public function getDemoUsers()
    {
        return [
            // 1 Kepala Sekolah (Pass: kepalasekolah101)
            [
                'id' => 1,
                'role_id' => 1,
                'name' => 'Drs. H. Hendra M.Pd',
                'full_name' => 'Drs. H. Hendra M.Pd',
                'username' => 'kepala_sekolah',
                'email' => 'kepsek@sch.id',
                'password' => '$2y$10$K.vuNGTKWu.sRXJbMPeeGOqwBcX3Q5rAa4ARVNYj1v4zX7pR9UjLC',
                'role' => 'kepala_sekolah',
                'phone' => '081100001111'
            ],
            // 1 Operator (Pass: operator456)
            [
                'id' => 2,
                'role_id' => 2,
                'name' => 'Siti Rahmawati, S.Kom',
                'full_name' => 'Siti Rahmawati, S.Kom',
                'username' => 'operator',
                'email' => 'operator@tkj.sch.id',
                'password' => '$2y$10$J6wtAl21qtRm4183NQjLp.qruHZ8hyM8nnmeS/Kroh2x1gaSQov7m',
                'role' => 'operator',
                'phone' => '081298765432'
            ],
            // 3 Teknisi (Pass: teknisi789)
            [
                'id' => 3,
                'role_id' => 3,
                'name' => 'Budi Santoso (Teknisi 1)',
                'full_name' => 'Budi Santoso',
                'username' => 'teknisi',
                'email' => 'budi.teknisi@tkj.sch.id',
                'password' => '$2y$10$aSZxsPdvdCOxB5o8.dQIBO9oXoCru/zmmIXmKI4PxVaE0o20OlycW',
                'role' => 'teknisi',
                'phone' => '085711223344'
            ],
            [
                'id' => 4,
                'role_id' => 3,
                'name' => 'Rizky Pratama (Teknisi 2)',
                'full_name' => 'Rizky Pratama',
                'username' => 'teknisi2',
                'email' => 'rizky.teknisi@tkj.sch.id',
                'password' => '$2y$10$aSZxsPdvdCOxB5o8.dQIBO9oXoCru/zmmIXmKI4PxVaE0o20OlycW',
                'role' => 'teknisi',
                'phone' => '085722334455'
            ],
            [
                'id' => 5,
                'role_id' => 3,
                'name' => 'Fajar Kurniawan (Teknisi 3)',
                'full_name' => 'Fajar Kurniawan',
                'username' => 'fajar.teknisi@tkj.sch.id',
                'password' => '$2y$10$aSZxsPdvdCOxB5o8.dQIBO9oXoCru/zmmIXmKI4PxVaE0o20OlycW',
                'role' => 'teknisi',
                'phone' => '085733445566'
            ],
            // 3 Customers (Pass: customer123)
            [
                'id' => 6,
                'role_id' => 4,
                'name' => 'Ahmad Dahlan',
                'full_name' => 'Ahmad Dahlan',
                'username' => 'customer',
                'email' => 'ahmad.dahlan@gmail.com',
                'password' => '$2y$10$BC7JFcyRtxp0Ne7za4gsluJqYLbKC1Fx5NWD04F834QlO7rBgLH86',
                'role' => 'customer',
                'phone' => '081234567890'
            ],
            [
                'id' => 7,
                'role_id' => 4,
                'name' => 'Dewi Lestari',
                'full_name' => 'Dewi Lestari',
                'username' => 'customer2',
                'email' => 'dewi.lestari@gmail.com',
                'password' => '$2y$10$BC7JFcyRtxp0Ne7za4gsluJqYLbKC1Fx5NWD04F834QlO7rBgLH86',
                'role' => 'customer',
                'phone' => '081245678901'
            ],
            [
                'id' => 8,
                'role_id' => 4,
                'name' => 'Budi Utomo (SDN 1)',
                'full_name' => 'Budi Utomo (SD Negeri 1)',
                'username' => 'customer3',
                'email' => 'sdn1.budiutomo@sch.id',
                'password' => '$2y$10$BC7JFcyRtxp0Ne7za4gsluJqYLbKC1Fx5NWD04F834QlO7rBgLH86',
                'role' => 'customer',
                'phone' => '081256789012'
            ]
        ];
    }

    /**
     * Get all users for operator panel with optional search and role filter.
     * Returns safe fields only (no password, tokens, OTP codes).
     */
    public function getAllUsersForOperator($search = '', $roleFilter = '')
    {
        if ($this->db->isConnected()) {
            $sql = "SELECT u.id, u.username, u.full_name, u.email, u.phone, u.is_active, u.created_at,
                           r.name as role, r.display_name as role_display
                    FROM {$this->table} u
                    JOIN roles r ON u.role_id = r.id
                    WHERE 1=1";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (u.full_name LIKE :search OR u.username LIKE :search2 OR u.email LIKE :search3)";
                $params[':search']  = "%{$search}%";
                $params[':search2'] = "%{$search}%";
                $params[':search3'] = "%{$search}%";
            }

            if (!empty($roleFilter)) {
                $sql .= " AND r.name = :role";
                $params[':role'] = $roleFilter;
            }

            $sql .= " ORDER BY r.id ASC, u.full_name ASC";

            $this->db->query($sql);
            foreach ($params as $key => $val) {
                $this->db->bind($key, $val);
            }

            return $this->db->resultSet() ?: [];
        }

        // Fallback: filter demo users (without password)
        $users = array_map(function ($u) {
            return [
                'id'           => $u['id'] ?? null,
                'username'     => $u['username'] ?? '',
                'full_name'    => $u['full_name'] ?? ($u['name'] ?? ''),
                'email'        => $u['email'] ?? '',
                'phone'        => $u['phone'] ?? '-',
                'is_active'    => 1,
                'created_at'   => '2026-01-01 00:00:00',
                'role'         => $u['role'] ?? 'customer',
                'role_display' => ucfirst(str_replace('_', ' ', $u['role'] ?? 'customer'))
            ];
        }, $this->getDemoUsers());

        if (!empty($search)) {
            $s = strtolower($search);
            $users = array_filter($users, function ($u) use ($s) {
                return strpos(strtolower($u['full_name']), $s) !== false
                    || strpos(strtolower($u['username']), $s) !== false
                    || strpos(strtolower($u['email']), $s) !== false;
            });
        }

        if (!empty($roleFilter)) {
            $users = array_filter($users, function ($u) use ($roleFilter) {
                return $u['role'] === $roleFilter;
            });
        }

        return array_values($users);
    }

    /**
     * Count all active users.
     */
    public function countAllActiveUsers()
    {
        if ($this->db->isConnected()) {
            $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE is_active = 1");
            $result = $this->db->single();
            return $result['total'] ?? 0;
        }
        return count($this->getDemoUsers());
    }

    /**
     * Create a new user (Operator feature)
     * Handles unique checks, password hashing, user insertion, and role-specific profile creation.
     */
    public function createUser($data)
    {
        $fullName = trim($data['full_name'] ?? '');
        $username = strtolower(trim($data['username'] ?? ''));
        $email    = strtolower(trim($data['email'] ?? ''));
        $phone    = trim($data['phone'] ?? '');
        $roleId   = intval($data['role_id'] ?? 0);
        $password = trim($data['password'] ?? '');

        if (empty($fullName) || empty($username) || empty($email) || empty($roleId) || empty($password)) {
            return ['status' => false, 'message' => 'Semua kolom wajib diisi lengkap.'];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return ['status' => false, 'message' => 'Format email tidak valid.'];
        }

        if (strlen($password) < 6) {
            return ['status' => false, 'message' => 'Password minimal harus 6 karakter.'];
        }

        if ($this->db->isConnected()) {
            // Check username uniqueness
            $this->db->query("SELECT id FROM {$this->table} WHERE LOWER(username) = :username LIMIT 1");
            $this->db->bind(':username', $username);
            if ($this->db->single()) {
                return ['status' => false, 'message' => "Username '@{$username}' sudah digunakan oleh pengguna lain."];
            }

            // Check email uniqueness
            $this->db->query("SELECT id FROM {$this->table} WHERE LOWER(email) = :email LIMIT 1");
            $this->db->bind(':email', $email);
            if ($this->db->single()) {
                return ['status' => false, 'message' => "Email '{$email}' sudah terdaftar dalam sistem."];
            }

            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Insert new user
            $insertSql = "INSERT INTO {$this->table} (role_id, username, email, password, full_name, phone, is_active, created_at)
                          VALUES (:role_id, :username, :email, :password, :full_name, :phone, 1, NOW())";
            $this->db->query($insertSql);
            $this->db->bind(':role_id', $roleId);
            $this->db->bind(':username', $username);
            $this->db->bind(':email', $email);
            $this->db->bind(':password', $hashedPassword);
            $this->db->bind(':full_name', $fullName);
            $this->db->bind(':phone', $phone);

            if ($this->db->execute()) {
                $newUserId = $this->db->lastInsertId();

                // If customer, create extended profile if customers table exists
                if ($roleId == 4) {
                    $this->db->query("INSERT INTO customers (user_id, customer_type, address, city, created_at) VALUES (:user_id, 'umum', 'Alamat belum diatur', 'Kota', NOW())");
                    $this->db->bind(':user_id', $newUserId);
                    $this->db->execute();
                }

                // If technician, create extended profile if technicians table exists
                if ($roleId == 3) {
                    $specialization = trim($data['specialization'] ?? 'Teknisi Jaringan & Hardware');
                    $this->db->query("INSERT INTO technicians (user_id, nis_nip, class_grade, specialization, skill_level, status, created_at) VALUES (:user_id, NULL, 'XII TKJ', :specialization, 'junior', 'available', NOW())");
                    $this->db->bind(':user_id', $newUserId);
                    $this->db->bind(':specialization', $specialization ?: 'Teknisi Jaringan & Hardware');
                    $this->db->execute();
                }

                return ['status' => true, 'message' => "Pengguna baru '{$fullName}' berhasil ditambahkan ke sistem."];
            } else {
                return ['status' => false, 'message' => 'Gagal menyimpan data pengguna ke database. ' . $this->db->getError()];
            }
        }

        return ['status' => true, 'message' => "Pengguna baru '{$fullName}' berhasil didaftarkan (Mode Demo)."];
    }
}
