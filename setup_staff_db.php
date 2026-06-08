<?php
require_once __DIR__ . '/config/db.php';

$queries = [
    "CREATE TABLE IF NOT EXISTS `staff` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `name` varchar(100) NOT NULL,
      `phone` varchar(20) NOT NULL,
      `email` varchar(100) DEFAULT NULL,
      `role` varchar(50) NOT NULL,
      `address` text DEFAULT NULL,
      `basic_salary` decimal(10,2) NOT NULL DEFAULT 0.00,
      `joining_date` date NOT NULL,
      `status` enum('Active','Inactive') NOT NULL DEFAULT 'Active',
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

    "CREATE TABLE IF NOT EXISTS `staff_attendance` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `staff_id` int(11) NOT NULL,
      `attendance_date` date NOT NULL,
      `status` enum('Present','Absent','Half-Day') NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `staff_date_unique` (`staff_id`, `attendance_date`),
      CONSTRAINT `fk_attendance_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

    "CREATE TABLE IF NOT EXISTS `staff_advances` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `staff_id` int(11) NOT NULL,
      `advance_date` date NOT NULL,
      `amount` decimal(10,2) NOT NULL,
      `reason` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      CONSTRAINT `fk_advance_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",
    
    "CREATE TABLE IF NOT EXISTS `staff_salary_deductions` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `staff_id` int(11) NOT NULL,
      `deduction_month` varchar(7) NOT NULL, -- Format: YYYY-MM
      `amount` decimal(10,2) NOT NULL,
      `reason` varchar(255) DEFAULT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      CONSTRAINT `fk_deduction_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;",

    "CREATE TABLE IF NOT EXISTS `staff_salary` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `staff_id` int(11) NOT NULL,
      `month_year` varchar(7) NOT NULL, -- Format: YYYY-MM
      `basic_salary` decimal(10,2) NOT NULL,
      `advance_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
      `absent_deduction` decimal(10,2) NOT NULL DEFAULT 0.00,
      `other_deductions` decimal(10,2) NOT NULL DEFAULT 0.00,
      `net_paid` decimal(10,2) NOT NULL,
      `payment_date` date NOT NULL,
      `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
      PRIMARY KEY (`id`),
      UNIQUE KEY `staff_month_unique` (`staff_id`, `month_year`),
      CONSTRAINT `fk_salary_staff` FOREIGN KEY (`staff_id`) REFERENCES `staff` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;"
];

$success = true;
foreach ($queries as $index => $sql) {
    if ($conn->query($sql) === TRUE) {
        echo "Query " . ($index + 1) . " executed successfully.\n";
    } else {
        echo "Error in query " . ($index + 1) . ": " . $conn->error . "\n";
        $success = false;
    }
}

if ($success) {
    echo "All tables created successfully!\n";
} else {
    echo "Some errors occurred.\n";
}
?>
