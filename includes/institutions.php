<?php
// Shared partner institutions data list
if (!isset($pdo)) {
    $dbPath = __DIR__ . '/db.php';
    if (file_exists($dbPath)) {
        require_once $dbPath;
    }
}

$institutions = [];
$regions = ['All'];

if (isset($pdo) && $pdo) {
    try {
        // Fetch all active institutions from database
        $stmt = $pdo->query("SELECT id, name, city, region, img, fees, admission_process, location_detail, courses, is_top, status, display_order FROM institutions WHERE status = 'Active' ORDER BY display_order ASC, region ASC, name ASC");
        $institutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Get unique regions of active institutions
        $stmtReg = $pdo->query("SELECT DISTINCT region FROM institutions WHERE status = 'Active' ORDER BY region ASC");
        $dbRegions = $stmtReg->fetchAll(PDO::FETCH_COLUMN);
        
        // Merge regions, putting 'All' at the first position
        foreach ($dbRegions as $reg) {
            if (!empty($reg) && $reg !== 'All') {
                $regions[] = $reg;
            }
        }
    } catch (PDOException $e) {
        // Safe fallback
    }
}

// Provide rich fallback institutions if DB returns empty
if (empty($institutions)) {
    $institutions = [
        ['id' => 1, 'name' => 'SRM Institute of Science and Technology', 'city' => 'Chennai', 'region' => 'South India', 'img' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60', 'is_top' => 1, 'courses' => '1,9,14,2'],
        ['id' => 2, 'name' => 'Chandigarh University', 'city' => 'Mohali / Chandigarh', 'region' => 'North India', 'img' => 'https://images.unsplash.com/photo-1592280771190-3e2e4d571952?w=600&auto=format&fit=crop&q=60', 'is_top' => 1, 'courses' => '1,2,6,9,14'],
        ['id' => 3, 'name' => 'Lovely Professional University (LPU)', 'city' => 'Jalandhar', 'region' => 'North India', 'img' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=600&auto=format&fit=crop&q=60', 'is_top' => 1, 'courses' => '1,2,4,6,9,14,16'],
        ['id' => 4, 'name' => 'Parul University', 'city' => 'Vadodara', 'region' => 'West India', 'img' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&auto=format&fit=crop&q=60', 'is_top' => 1, 'courses' => '1,2,3,6,8,9,14'],
        ['id' => 5, 'name' => 'Sharda University', 'city' => 'Greater Noida (Delhi NCR)', 'region' => 'North India', 'img' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=600&auto=format&fit=crop&q=60', 'is_top' => 1, 'courses' => '1,2,3,9,10,14'],
        ['id' => 6, 'name' => 'Bangalore Group of Institutions', 'city' => 'Bangalore', 'region' => 'South India', 'img' => 'https://images.unsplash.com/photo-1527891751199-7225231a68dd?w=600&auto=format&fit=crop&q=60', 'is_top' => 1, 'courses' => '2,6,8,9,14,18'],
        ['id' => 7, 'name' => 'Manipal Academy of Higher Education', 'city' => 'Manipal', 'region' => 'South India', 'img' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&auto=format&fit=crop&q=60', 'is_top' => 1, 'courses' => '1,2,3,9,10,14'],
        ['id' => 8, 'name' => 'Alliance University', 'city' => 'Bangalore', 'region' => 'South India', 'img' => 'https://images.unsplash.com/photo-1519452635265-7b1fbfd1e4e0?w=600&auto=format&fit=crop&q=60', 'is_top' => 0, 'courses' => '9,12,14,1']
    ];
    $regions = ['All', 'North India', 'South India', 'West India', 'East India'];
}
?>
