<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../logs/log_config.php';


// Récupération des logs
$log_types = ['application' => 'app_*.log', 'security' => 'security_*.log'];
$current_type = $_GET['type'] ?? 'application';
$current_file = $_GET['file'] ?? '';

// Validation du type
if (!array_key_exists($current_type, $log_types)) {
    $current_type = 'application';
}

// Récupération des fichiers disponibles
$available_files = glob(LOG_DIR . $log_types[$current_type]);
rsort($available_files);

// Sélection du fichier (le plus récent par défaut)
if (empty($current_file) || !in_array($current_file, $available_files)) {
    $current_file = $available_files[0] ?? '';
}

// Lecture des logs avec pagination
$logs = [];
$stats = ['total' => 0, 'levels' => array_fill_keys(array_keys(LOG_LEVELS), 0)];

if ($current_file && file_exists($current_file)) {
    $file_content = file_get_contents($current_file);
    $lines = explode("\n", trim($file_content));
    
    foreach ($lines as $line) {
        if (preg_match('/^\[(?<date>.+?)\] \[?(?<level>\w+)\]? (?<message>.+?)(?<context> \{.+?\})?$/', $line, $matches)) {
            $stats['total']++;
            $stats['levels'][$matches['level']]++;
            
            $logs[] = [
                'timestamp' => $matches['date'],
                'level' => $matches['level'],
                'message' => $matches['message'],
                'context' => isset($matches['context']) ? json_decode($matches['context'], true) : [],
                'raw' => $line
            ];
        }
    }
}

// Filtrage
$filters = [
    'level' => $_GET['level'] ?? '',
    'search' => trim($_GET['search'] ?? ''),
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? ''
];

$filtered_logs = array_filter($logs, function($log) use ($filters) {
    // Filtre par niveau
    if ($filters['level'] && $log['level'] !== $filters['level']) {
        return false;
    }
    
    // Filtre par recherche
    if ($filters['search'] && 
        !(stripos($log['message'], $filters['search']) !== false || 
         stripos($log['raw'], $filters['search']) !== false)) {
        return false;
    }
    
    // Filtre par date
    $log_time = strtotime($log['timestamp']);
    if ($filters['date_from'] && $log_time < strtotime($filters['date_from'])) {
        return false;
    }
    if ($filters['date_to'] && $log_time > strtotime($filters['date_to'] . ' 23:59:59')) {
        return false;
    }
    
    return true;
});

// Pagination
$per_page = 50;
$total_logs = count($filtered_logs);
$total_pages = ceil($total_logs / $per_page);
$current_page = min(max(1, intval($_GET['page'] ?? 1)), $total_pages);
$offset = ($current_page - 1) * $per_page;
$paginated_logs = array_slice($filtered_logs, $offset, $per_page);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supervision des Logs</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --color-primary: #6366f1;
            --color-danger: #ef4444;
            --color-warning: #f59e0b;
            --color-success: #10b981;
            --color-info: #3b82f6;
            --color-dark: #1f2937;
            --color-light: #f3f4f6;
            --color-gray: #6b7280;
            --color-bg: #f9fafb;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-bg);
            color: var(--color-dark);
            line-height: 1.6;
        }
        
        .container {
            max-width: 1800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        h1 {
            font-size: 24px;
            font-weight: 600;
            color: var(--color-primary);
        }
        
        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            padding: 24px;
            margin-bottom: 24px;
        }
        
        .tabs {
            display: flex;
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 20px;
        }
        
        .tab {
            padding: 12px 20px;
            cursor: pointer;
            font-weight: 500;
            color: var(--color-gray);
            border-bottom: 3px solid transparent;
            transition: all 0.2s;
        }
        
        .tab.active {
            color: var(--color-primary);
            border-bottom-color: var(--color-primary);
        }
        
        .tab:hover:not(.active) {
            color: var(--color-dark);
            border-bottom-color: #e5e7eb;
        }
        
        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
        }
        
        label {
            font-size: 14px;
            font-weight: 500;
            color: var(--color-gray);
            margin-bottom: 8px;
        }
        
        select, input {
            padding: 10px 12px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.2s;
        }
        
        select:focus, input:focus {
            outline: none;
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            background-color: var(--color-primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            gap: 8px;
        }
        
        .btn:hover {
            background-color: #4f46e5;
        }
        
        .btn-outline {
            background: transparent;
            border: 1px solid #e5e7eb;
            color: var(--color-dark);
        }
        
        .btn-outline:hover {
            background: #f9fafb;
            border-color: #d1d5db;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 16px;
            margin-bottom: 24px;
        }
        
        .stat-card {
            padding: 16px;
            border-radius: 8px;
            color: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        
        .stat-card.total { background: linear-gradient(135deg, var(--color-dark), #374151); }
        .stat-card.debug { background: linear-gradient(135deg, var(--color-gray), #9ca3af); }
        .stat-card.info { background: linear-gradient(135deg, var(--color-info), #60a5fa); }
        .stat-card.warning { background: linear-gradient(135deg, var(--color-warning), #fbbf24); }
        .stat-card.error { background: linear-gradient(135deg, var(--color-danger), #f87171); }
        .stat-card.critical { background: linear-gradient(135deg, #7c3aed, #a78bfa); }
        
        .stat-card h3 {
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }
        
        .stat-card p {
            font-size: 24px;
            font-weight: 600;
        }
        
        .log-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        
        .log-table th {
            text-align: left;
            padding: 12px 16px;
            background-color: #f9fafb;
            font-weight: 500;
            color: var(--color-gray);
            position: sticky;
            top: 0;
        }
        
        .log-table td {
            padding: 12px 16px;
            border-bottom: 1px solid #f3f4f6;
            vertical-align: top;
        }
        
        .log-table tr:hover td {
            background-color: #f9fafb;
        }
        
        .log-level {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            gap: 4px;
        }
        
        .level-debug { background-color: #f3f4f6; color: var(--color-gray); }
        .level-info { background-color: #dbeafe; color: var(--color-info); }
        .level-notice { background-color: #e0f2fe; color: #0ea5e9; }
        .level-warning { background-color: #fef3c7; color: var(--color-warning); }
        .level-error { background-color: #fee2e2; color: var(--color-danger); }
        .level-critical { background-color: #ede9fe; color: #7c3aed; }
        .level-alert { background-color: #ffedd5; color: #f97316; }
        .level-emergency { background-color: #fecaca; color: #b91c1c; }
        
        .log-message {
            white-space: pre-wrap;
            word-break: break-word;
            font-family: 'JetBrains Mono', monospace;
            font-size: 13px;
        }
        
        .log-context {
            font-family: 'JetBrains Mono', monospace;
            font-size: 12px;
            color: var(--color-gray);
            white-space: pre-wrap;
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .log-context.expanded {
            max-width: none;
            white-space: pre;
            overflow: auto;
            background: #f9fafb;
            padding: 8px;
            border-radius: 4px;
            border: 1px solid #e5e7eb;
        }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            margin-top: 24px;
            flex-wrap: wrap;
        }
        
        .pagination a, .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 8px;
            border-radius: 6px;
        }
        
        .pagination a {
            color: var(--color-dark);
            text-decoration: none;
            border: 1px solid #e5e7eb;
            transition: all 0.2s;
        }
        
        .pagination a:hover {
            background-color: #f3f4f6;
            border-color: #d1d5db;
        }
        
        .pagination .current {
            background-color: var(--color-primary);
            color: white;
            border-color: var(--color-primary);
        }
        
        .file-selector {
            margin-bottom: 24px;
        }
        
        .file-selector select {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1px solid #e5e7eb;
            background-color: white;
            font-size: 14px;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--color-gray);
        }
        
        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            color: #e5e7eb;
        }
        
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
            background-color: #f3f4f6;
            color: var(--color-gray);
            gap: 4px;
        }
        
        .badge i {
            font-size: 12px;
        }
        
        @media (max-width: 768px) {
            .stats {
                grid-template-columns: 1fr 1fr;
            }
            
            .filters {
                grid-template-columns: 1fr;
            }
            
            .log-table {
                display: block;
                overflow-x: auto;
            }
        }
        
        /* Animation */
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        .loader {
            width: 20px;
            height: 20px;
            border: 3px solid rgba(0,0,0,0.1);
            border-top-color: var(--color-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
    </style>
</head>
<body>
    <header>
        <div class="container header-content">
            <h1><i class="fas fa-clipboard-list"></i> Tableau de bord des logs</h1>
            <div>
                <a href="dashboard.php" class="btn btn-outline">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </header>
    
    <main class="container">
        <div class="card">
            <div class="tabs">
                <div class="tab <?= $current_type === 'application' ? 'active' : '' ?>" 
                     onclick="window.location.href='?type=application'">
                    <i class="fas fa-file-alt"></i> Logs Application
                </div>
                <div class="tab <?= $current_type === 'security' ? 'active' : '' ?>" 
                     onclick="window.location.href='?type=security'">
                    <i class="fas fa-shield-alt"></i> Logs Sécurité
                </div>
            </div>
            
            <div class="file-selector">
                <select onchange="window.location.href='?type=<?= $current_type ?>&file='+this.value">
                    <?php foreach ($available_files as $file): ?>
                        <option value="<?= htmlspecialchars(basename($file)) ?>" 
                                <?= $file === $current_file ? 'selected' : '' ?>>
                            <?= htmlspecialchars(basename($file)) ?>
                            (<?= date('d/m/Y H:i', filemtime($file)) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="stats">
                <div class="stat-card total">
                    <h3>Total des entrées</h3>
                    <p><?= number_format($stats['total']) ?></p>
                </div>
                <?php foreach (LOG_LEVELS as $level => $severity): ?>
                    <?php if ($stats['levels'][$level] > 0): ?>
                        <div class="stat-card <?= strtolower($level) ?>">
                            <h3><?= $level ?></h3>
                            <p><?= number_format($stats['levels'][$level]) ?></p>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
            
            <form method="get" class="filters">
                <input type="hidden" name="type" value="<?= htmlspecialchars($current_type) ?>">
                <input type="hidden" name="file" value="<?= htmlspecialchars(basename($current_file)) ?>">
                
                <div class="filter-group">
                    <label for="level">Niveau de log</label>
                    <select name="level" id="level">
                        <option value="">Tous les niveaux</option>
                        <?php foreach (LOG_LEVELS as $level => $severity): ?>
                            <option value="<?= $level ?>" <?= $filters['level'] === $level ? 'selected' : '' ?>>
                                <?= $level ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="search">Recherche texte</label>
                    <input type="text" name="search" id="search" placeholder="Rechercher..." 
                           value="<?= htmlspecialchars($filters['search']) ?>">
                </div>
                
                <div class="filter-group">
                    <label for="date_from">Date début</label>
                    <input type="date" name="date_from" id="date_from" 
                           value="<?= htmlspecialchars($filters['date_from']) ?>">
                </div>
                
                <div class="filter-group">
                    <label for="date_to">Date fin</label>
                    <input type="date" name="date_to" id="date_to" 
                           value="<?= htmlspecialchars($filters['date_to']) ?>">
                </div>
                
                <div class="filter-group" style="align-self: flex-end;">
                    <button type="submit" class="btn">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    <a href="?type=<?= $current_type ?>&file=<?= urlencode(basename($current_file)) ?>" 
                       class="btn btn-outline" style="margin-left: 8px;">
                        <i class="fas fa-times"></i> Réinitialiser
                    </a>
                </div>
            </form>
            
            <?php if (empty($paginated_logs)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3>Aucune entrée de log trouvée</h3>
                    <p>Essayez d'ajuster vos filtres ou de sélectionner un autre fichier</p>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="log-table">
                        <thead>
                            <tr>
                                <th>Date/Heure</th>
                                <th>Niveau</th>
                                <th>Message</th>
                                <th>Contexte</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($paginated_logs as $log): ?>
                                <tr>
                                    <td><?= htmlspecialchars($log['timestamp']) ?></td>
                                    <td>
                                        <span class="log-level level-<?= strtolower($log['level']) ?>">
                                            <i class="fas fa-<?= get_icon_for_level($log['level']) ?>"></i>
                                            <?= $log['level'] ?>
                                        </span>
                                    </td>
                                    <td class="log-message"><?= htmlspecialchars($log['message']) ?></td>
                                    <td class="log-context" id="context-<?= md5($log['raw']) ?>">
                                        <?php if (!empty($log['context'])): ?>
                                            <?= htmlspecialchars(json_encode($log['context'], JSON_PRETTY_PRINT)) ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($log['context'])): ?>
                                            <button class="badge" 
                                                    onclick="toggleContext('context-<?= md5($log['raw']) ?>')">
                                                <i class="fas fa-expand"></i> Détail
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <?php if ($current_page > 1): ?>
                            <a href="?<?= build_query_string(['page' => 1]) ?>">
                                <i class="fas fa-angle-double-left"></i>
                            </a>
                            <a href="?<?= build_query_string(['page' => $current_page - 1]) ?>">
                                <i class="fas fa-angle-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php 
                        $start = max(1, $current_page - 2);
                        $end = min($total_pages, $current_page + 2);
                        
                        if ($start > 1): ?>
                            <span>...</span>
                        <?php endif;
                        
                        for ($i = $start; $i <= $end; $i++): ?>
                            <?php if ($i === $current_page): ?>
                                <span class="current"><?= $i ?></span>
                            <?php else: ?>
                                <a href="?<?= build_query_string(['page' => $i]) ?>"><?= $i ?></a>
                            <?php endif; ?>
                        <?php endfor;
                        
                        if ($end < $total_pages): ?>
                            <span>...</span>
                        <?php endif; ?>
                        
                        <?php if ($current_page < $total_pages): ?>
                            <a href="?<?= build_query_string(['page' => $current_page + 1]) ?>">
                                <i class="fas fa-angle-right"></i>
                            </a>
                            <a href="?<?= build_query_string(['page' => $total_pages]) ?>">
                                <i class="fas fa-angle-double-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>
    
    <script>
        function toggleContext(id) {
            const element = document.getElementById(id);
            element.classList.toggle('expanded');
        }
        
        function buildQueryString(params) {
            const currentParams = new URLSearchParams(window.location.search);
            for (const [key, value] of Object.entries(params)) {
                currentParams.set(key, value);
            }
            return currentParams.toString();
        }
        
        // Rafraîchissement automatique toutes les 30 secondes
        setTimeout(() => {
            window.location.reload();
        }, 30000);
    </script>
</body>
</html>

<?php
// Fonctions helper
function get_icon_for_level($level) {
    $icons = [
        'DEBUG'     => 'bug',
        'INFO'      => 'info-circle',
        'NOTICE'    => 'info-circle',
        'WARNING'   => 'exclamation-triangle',
        'ERROR'     => 'times-circle',
        'CRITICAL'  => 'radiation',
        'ALERT'     => 'bell',
        'EMERGENCY' => 'skull-crossbones'
    ];
    return $icons[strtoupper($level)] ?? 'circle';
}

function build_query_string($new_params = []) {
    $params = array_merge($_GET, $new_params);
    return http_build_query($params);
}
