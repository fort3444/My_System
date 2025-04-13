<?php
require_once 'config.php';

// Handle deletion
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    
    // First get the image path
    $stmt = $pdo->prepare("SELECT image_path FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Delete the image file
        $imagePath = "uploads/" . $user['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
        
        // Delete the record
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
    }
    
    header('Location: index.php');
    exit();
}

// Fetch all users
$stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>UserHub | Modern User Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --accent-color: #4cc9f0;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4bb543;
            --danger-color: #ff3333;
            --warning-color: #ffbe0b;
            --border-radius: 8px;
            --box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            color: var(--dark-color);
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 30px;
            background-color: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            position: relative;
            overflow: hidden;
        }
        
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 8px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
        }
        
        h1 {
            color: var(--dark-color);
            margin-bottom: 25px;
            text-align: center;
            padding-bottom: 15px;
            position: relative;
            font-weight: 700;
            font-size: 2.2rem;
        }
        
        h1::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100px;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-color), var(--accent-color));
            border-radius: 2px;
        }
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }
        
        .search-container {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 50px;
            padding: 8px 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
            transition: var(--transition);
        }
        
        .search-container:focus-within {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .search-container input {
            border: none;
            outline: none;
            padding: 5px 10px;
            font-size: 16px;
            width: 250px;
        }
        
        .search-container i {
            color: #6c757d;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 12px 24px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 50px;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            gap: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .btn:hover {
            background-color: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: 0 7px 14px rgba(0, 0, 0, 0.15);
        }
        
        .btn i {
            font-size: 14px;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
        }
        
        .btn-danger:hover {
            background-color: #e60000;
        }
        
        .btn-warning {
            background-color: var(--warning-color);
            color: var(--dark-color);
        }
        
        .btn-warning:hover {
            background-color: #ffaa00;
        }
        
        .table-container {
            overflow-x: auto;
            border-radius: var(--border-radius);
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            position: relative;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 800px;
        }
        
        th {
            position: sticky;
            top: 0;
            background-color: white;
            color: var(--dark-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            padding: 18px 15px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        td {
            padding: 15px;
            border-bottom: 1px solid #f0f0f0;
            vertical-align: middle;
        }
        
        tr:last-child td {
            border-bottom: none;
        }
        
        tr:hover td {
            background-color: #f8faff;
        }
        
        .status {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-active {
            background-color: rgba(75, 181, 67, 0.1);
            color: var(--success-color);
        }
        
        .status-inactive {
            background-color: rgba(255, 51, 51, 0.1);
            color: var(--danger-color);
        }
        
        .profile-img-container {
            position: relative;
            width: 60px;
            height: 60px;
        }
        
        .profile-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
        }
        
        tr:hover .profile-img {
            transform: scale(1.1);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }
        
        .no-image {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #f0f0f0;
            color: #999;
            font-size: 24px;
        }
        
        .actions {
            display: flex;
            gap: 8px;
        }
        
        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background-color: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            cursor: pointer;
            transition: var(--transition);
        }
        
        .action-btn:hover {
            background-color: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }
        
        .action-btn.delete {
            color: var(--danger-color);
            border-color: var(--danger-color);
        }
        
        .action-btn.delete:hover {
            background-color: var(--danger-color);
            color: white;
        }
        
        .action-btn.edit {
            color: var(--warning-color);
            border-color: var(--warning-color);
        }
        
        .action-btn.edit:hover {
            background-color: var(--warning-color);
            color: white;
        }
        
        .no-users {
            text-align: center;
            padding: 50px 20px;
            color: #6c757d;
        }
        
        .no-users i {
            font-size: 50px;
            color: #d1d5db;
            margin-bottom: 20px;
        }
        
        .no-users h3 {
            font-size: 24px;
            margin-bottom: 10px;
            color: var(--dark-color);
        }
        
        .no-users p {
            font-size: 16px;
            margin-bottom: 20px;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 8px;
        }
        
        .page-item {
            display: flex;
        }
        
        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: white;
            color: var(--dark-color);
            text-decoration: none;
            border: 1px solid #e0e0e0;
            transition: var(--transition);
        }
        
        .page-link:hover, .page-link.active {
            background-color: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }
        
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            display: flex;
            flex-direction: column;
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-card h3 {
            font-size: 14px;
            color: #6c757d;
            margin-bottom: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .stat-card p {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark-color);
        }
        
        .stat-card i {
            align-self: flex-end;
            font-size: 40px;
            opacity: 0.2;
            margin-top: -20px;
            color: var(--primary-color);
        }
        
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }
            
            h1 {
                font-size: 1.8rem;
            }
            
            .header-actions {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-container {
                width: 100%;
            }
            
            .search-container input {
                width: 100%;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-users"></i> UserHub Dashboard
        </h1>
        
        <div class="stats-container">
            <div class="stat-card">
                <h3>Total Users</h3>
                <p><?= count($users) ?></p>
                <i class="fas fa-user-friends"></i>
            </div>
            <div class="stat-card">
                <h3>Active Today</h3>
                <p>12</p>
                <i class="fas fa-bolt"></i>
            </div>
            <div class="stat-card">
                <h3>New This Week</h3>
                <p>5</p>
                <i class="fas fa-chart-line"></i>
            </div>
        </div>
        
        <div class="header-actions">
            <div class="search-container">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search users...">
            </div>
            <div>
                <a href="create.php" class="btn">
                    <i class="fas fa-plus"></i> Add New User
                </a>
            </div>
        </div>
        
        <?php if (count($users) > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Status</th>
                        <th>Joined</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                    <tr>
                        <td>#<?= htmlspecialchars($user['id']) ?></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 15px;">
                                <div class="profile-img-container">
                                    <?php if ($user['image_path']): ?>
                                        <img src="uploads/<?= htmlspecialchars($user['image_path']) ?>" alt="Profile" class="profile-img">
                                    <?php else: ?>
                                        <div class="no-image">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($user['name']) ?></div>
                                    <div style="font-size: 13px; color: #6c757d;">@<?= strtolower(str_replace(' ', '', htmlspecialchars($user['name']))) ?></div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 500;"><?= htmlspecialchars($user['email']) ?></div>
                            <div style="font-size: 13px; color: #6c757d;">
                                <i class="fas fa-phone"></i> +1 (555) 123-4567
                            </div>
                        </td>
                        <td>
                            <span class="status status-active">Active</span>
                        </td>
                        <td>
                            <?= date('M j, Y', strtotime(htmlspecialchars($user['created_at']))) ?>
                            <div style="font-size: 13px; color: #6c757d;">
                                <?= date('g:i A', strtotime(htmlspecialchars($user['created_at']))) ?>
                            </div>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="edit.php?id=<?= $user['id'] ?>" class="action-btn edit" title="Edit">
                                    <i class="fas fa-pencil-alt"></i>
                                </a>
                                <a href="index.php?delete=<?= $user['id'] ?>" class="action-btn delete" title="Delete" onclick="return confirm('Are you sure you want to delete this user?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <a href="view.php?id=<?= $user['id'] ?>" class="action-btn" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="pagination">
            <div class="page-item">
                <a href="#" class="page-link"><i class="fas fa-chevron-left"></i></a>
            </div>
            <div class="page-item">
                <a href="#" class="page-link active">1</a>
            </div>
            <div class="page-item">
                <a href="#" class="page-link">2</a>
            </div>
            <div class="page-item">
                <a href="#" class="page-link">3</a>
            </div>
            <div class="page-item">
                <a href="#" class="page-link"><i class="fas fa-chevron-right"></i></a>
            </div>
        </div>
        <?php else: ?>
            <div class="no-users">
                <i class="fas fa-user-slash"></i>
                <h3>No Users Found</h3>
                <p>It looks like your user database is empty. Get started by adding your first user.</p>
                <a href="create.php" class="btn" style="margin-top: 15px;">
                    <i class="fas fa-plus"></i> Create First User
                </a>
            </div>
        <?php endif; ?>
    </div>
    
    <script>
        // Simple animation for table rows
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                setTimeout(() => {
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 50);
            });
        });
    </script>
</body>
</html>