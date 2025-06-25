<?php
// Save this as updates.php

// Configuration
$updateFile = 'updates.txt';
$rssFile = 'updates.rss';
$siteTitle = 'Website Updates';
$siteUrl = 'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . '/';
$description = 'Latest changes and improvements';

// Read and parse updates
function readUpdates($file) {
    if (!file_exists($file)) return [];
    
    $content = file_get_contents($file);
    $entries = preg_split('/\n---+\n/', $content);
    
    $updates = [];
    foreach ($entries as $entry) {
        if (empty(trim($entry))) continue;
        
        // Parse metadata
        $metadata = [];
        $lines = explode("\n", $entry);
        foreach ($lines as $line) {
            if (preg_match('/^(\w+):\s*(.+)$/', $line, $matches)) {
                $metadata[$matches[1]] = trim($matches[2]);
            }
        }
        
        // Get content (all lines after metadata)
        $content = '';
        $inContent = false;
        foreach ($lines as $line) {
            if ($inContent) {
                $content .= $line . "\n";
            } elseif (trim($line) === '') {
                $inContent = true;
            }
        }
        
        // Set defaults
        $date = $metadata['date'] ?? date('Y-m-d');
        $time = $metadata['time'] ?? '12:00';
        $datetime = $date . ' ' . $time;
        
        $updates[] = [
            'datetime' => $datetime,
            'timestamp' => strtotime($datetime),
            'title' => $metadata['title'] ?? 'Update',
            'url' => $metadata['url'] ?? '',
            'content' => trim($content)
        ];
    }
    
    // Sort by date (newest first)
    usort($updates, function($a, $b) {
        return $b['timestamp'] - $a['timestamp'];
    });
    
    return $updates;
}

// Generate RSS feed
function generateRSS($updates, $file, $siteTitle, $siteUrl, $description) {
    $rss = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $rss .= '<rss version="2.0">' . "\n";
    $rss .= '<channel>' . "\n";
    $rss .= "<title>$siteTitle</title>\n";
    $rss .= "<link>$siteUrl</link>\n";
    $rss .= "<description>$description</description>\n";
    $rss .= "<generator>Manual Update Tracker</generator>\n";
    
    foreach ($updates as $update) {
        $link = !empty($update['url']) ? $update['url'] : $siteUrl;
        $pubDate = date('r', $update['timestamp']);
        
        $rss .= "<item>\n";
        $rss .= "<title>{$update['title']}</title>\n";
        $rss .= "<link>$link</link>\n";
        $rss .= "<description><![CDATA[{$update['content']}]]></description>\n";
        $rss .= "<pubDate>$pubDate</pubDate>\n";
        $rss .= "<guid>" . md5($update['title'] . $update['timestamp']) . "</guid>\n";
        $rss .= "</item>\n";
    }
    
    $rss .= "</channel>\n";
    $rss .= "</rss>";
    
    file_put_contents($file, $rss);
}

// Process updates
$updates = readUpdates($updateFile);
generateRSS($updates, $rssFile, $siteTitle, $siteUrl, $description);

// HTML Output
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($siteTitle) ?></title>
    <style>
        :root {
            --primary: #4361ee;
            --secondary: #3f37c9;
            --accent: #4895ef;
            --light: #f8f9fa;
            --dark: #212529;
            --success: #4cc9f0;
            --gray: #6c757d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e7f4 100%);
            color: var(--dark);
            line-height: 1.7;
            padding: 20px;
            min-height: 100vh;
        }
        
        .container {
            max-width: 800px;
            margin: 20px auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }
        
        header {
            background: linear-gradient(120deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 40px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        
        header::before {
            content: "";
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
            transform: rotate(30deg);
        }
        
        h1 {
            font-size: 2.8rem;
            margin-bottom: 12px;
            position: relative;
            z-index: 2;
        }
        
        .subtitle {
            font-size: 1.2rem;
            opacity: 0.92;
            max-width: 600px;
            margin: 0 auto 25px;
            position: relative;
            z-index: 2;
        }
        
        .header-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            position: relative;
            z-index: 2;
        }
        
        .btn {
            display: inline-block;
            padding: 12px 25px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1rem;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: white;
            color: var(--primary);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }
        
        .btn-primary:hover {
            background: #f0f0f0;
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.15);
            color: white;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-3px);
        }
        
        .update-list {
            padding: 30px;
        }
        
        .update {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
            border: 1px solid rgba(0, 0, 0, 0.04);
            transition: all 0.3s ease;
        }
        
        .update:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            border-color: rgba(var(--primary), 0.15);
        }
        
        .update-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .update-title {
            font-size: 1.6rem;
            color: var(--dark);
            font-weight: 700;
        }
        
        .update-title a {
            color: inherit;
            text-decoration: none;
            transition: all 0.2s;
        }
        
        .update-title a:hover {
            color: var(--primary);
        }
        
        .update-meta {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .update-datetime {
            background: rgba(var(--primary), 0.08);
            color: var(--primary);
            padding: 6px 12px;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .update-url {
            background: var(--light);
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 0.9rem;
            color: var(--gray);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            transition: all 0.2s;
        }
        
        .update-url:hover {
            background: #e9ecef;
            color: var(--dark);
        }
        
        .update-url span {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .update-content {
            color: var(--dark);
            line-height: 1.8;
            padding-top: 10px;
            border-top: 1px dashed rgba(0, 0, 0, 0.08);
            margin-top: 15px;
        }
        
        .update-content p {
            margin-bottom: 15px;
        }
        
        .update-content a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            border-bottom: 1px solid rgba(var(--primary), 0.3);
            transition: all 0.2s;
        }
        
        .update-content a:hover {
            color: var(--secondary);
            border-bottom-color: var(--secondary);
        }
        
        .empty-state {
            text-align: center;
            padding: 50px 20px;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: #dee2e6;
            margin-bottom: 20px;
        }
        
        .empty-title {
            font-size: 1.8rem;
            color: var(--gray);
            margin-bottom: 15px;
        }
        
        .empty-text {
            color: var(--gray);
            max-width: 500px;
            margin: 0 auto;
        }
        
        .format-guide {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 25px;
            margin: 30px 0;
            border: 1px dashed rgba(0, 0, 0, 0.08);
        }
        
        .guide-title {
            font-size: 1.3rem;
            color: var(--dark);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        pre {
            background: #2d2d2d;
            color: #f8f8f2;
            padding: 20px;
            border-radius: 8px;
            overflow-x: auto;
            font-size: 0.95rem;
            line-height: 1.5;
            margin: 15px 0;
            font-family: 'Fira Code', 'Courier New', monospace;
        }
        
        .token.comment {
            color: #6a9955;
        }
        
        .token.keyword {
            color: #569cd6;
        }
        
        footer {
            text-align: center;
            padding: 25px;
            color: var(--gray);
            font-size: 0.95rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        @media (max-width: 650px) {
            .update-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .update-meta {
                width: 100%;
                justify-content: space-between;
            }
            
            header {
                padding: 30px 15px;
            }
            
            h1 {
                font-size: 2.2rem;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <header>
            <h1><?= htmlspecialchars($siteTitle) ?></h1>
            <p class="subtitle"><?= htmlspecialchars($description) ?></p>
            <div class="header-buttons">
                <a href="<?= htmlspecialchars($rssFile) ?>" class="btn btn-primary">
                    <i class="fas fa-rss"></i> RSS Feed
                </a>
                <a href="#format-guide" class="btn btn-secondary">
                    <i class="fas fa-code"></i> Format Guide
                </a>
            </div>
        </header>
        
        <div class="update-list">
            <?php if (empty($updates)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="far fa-newspaper"></i>
                    </div>
                    <h3 class="empty-title">No updates yet</h3>
                    <p class="empty-text">Add your first update using the format guide below</p>
                </div>
            <?php else: ?>
                <?php foreach ($updates as $update): ?>
                    <div class="update">
                        <div class="update-header">
                            <h2 class="update-title">
                                <?php if (!empty($update['url'])): ?>
                                    <a href="<?= htmlspecialchars($update['url']) ?>">
                                        <?= htmlspecialchars($update['title']) ?>
                                    </a>
                                <?php else: ?>
                                    <?= htmlspecialchars($update['title']) ?>
                                <?php endif; ?>
                            </h2>
                            <div class="update-meta">
                                <div class="update-datetime">
                                    <i class="far fa-calendar"></i>
                                    <?= date('M j, Y', $update['timestamp']) ?> 
                                    <i class="far fa-clock"></i>
                                    <?= date('g:i a', $update['timestamp']) ?>
                                </div>
                                
                                <?php if (!empty($update['url'])): ?>
                                    <a href="<?= htmlspecialchars($update['url']) ?>" class="update-url">
                                        <i class="fas fa-link"></i>
                                        <span><?= htmlspecialchars(parse_url($update['url'], PHP_URL_HOST)) ?></span>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="update-content">
                            <?= $update['content'] ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="format-guide" id="format-guide">
            <h3 class="guide-title"><i class="fas fa-info-circle"></i> How to Add Updates</h3>
            <p>Edit the <code>updates.txt</code> file using this format:</p>
            
            <pre>title: Added new projects section
date: 2025-06-25
time: 14:30
url: /projects

I've created a new section to showcase my recent work. 
It includes filters and search functionality.

Check it out: &lt;a href="/projects"&gt;View Projects&lt;/a&gt;

---

title: Contact form fix
date: 2025-06-24
time: 10:15
url: /contact

Fixed the contact form that wasn't sending emails properly. 
Added spam protection with Google reCAPTCHA.

---

title: Mobile responsiveness improvements
date: 2025-06-23

Improved the mobile experience across all pages. 
The site now works perfectly on all device sizes.

Added new features:
&lt;ul&gt;
  &lt;li&gt;Better touch targets&lt;/li&gt;
  &lt;li&gt;Improved navigation&lt;/li&gt;
  &lt;li&gt;Faster loading on mobile networks&lt;/li&gt;
&lt;/ul&gt;</pre>
            
            <p><strong>Key features:</strong></p>
            <ul style="padding-left: 25px; margin: 15px 0; color: #495057;">
                <li>Each update is separated by <code>---</code> on a new line</li>
                <li>Include metadata lines (title, date, time, url)</li>
                <li>Add content below the metadata (supports HTML)</li>
                <li>Time is optional (defaults to 12:00)</li>
                <li>URL is optional (links the title when provided)</li>
            </ul>
        </div>
        
        <footer>
            <p>Updates maintained manually &bull; RSS feed generated automatically</p>
            <p>Last generated: <?= date('F j, Y \a\t g:i a') ?></p>
        </footer>
    </div>
</body>
</html>
