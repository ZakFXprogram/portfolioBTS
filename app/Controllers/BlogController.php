<?php
/**
 * Contrôleur du Blog
 */
class BlogController extends Controller
{
    // Flux RSS pour la veille sur la sécurité des API
    private $rssFeeds = [
        [
            'name' => 'CERT-FR (ANSSI)',
            'url' => 'https://www.cert.ssi.gouv.fr/feed/',
            'category' => 'Alertes Sécurité'
        ],
        [
            'name' => 'The Hacker News',
            'url' => 'https://feeds.feedburner.com/TheHackersNews',
            'category' => 'Actualités Cybersécurité'
        ],
        [
            'name' => 'Krebs on Security',
            'url' => 'https://krebsonsecurity.com/feed/',
            'category' => 'Sécurité Web'
        ],
        [
            'name' => 'OWASP Blog',
            'url' => 'https://owasp.org/feed.xml',
            'category' => 'Sécurité Applications'
        ],
        [
            'name' => 'Security Weekly',
            'url' => 'https://securityweekly.com/feed/',
            'category' => 'Actualités Sécurité'
        ],
        [
            'name' => 'Dark Reading',
            'url' => 'https://www.darkreading.com/rss.xml',
            'category' => 'Cybersécurité'
        ]
    ];

    public function index()
    {
        $posts = $this->db->fetchAll("SELECT * FROM posts WHERE published = 1 ORDER BY published_at DESC");

        // Récupérer les articles de veille depuis les flux RSS
        $veilleArticles = $this->fetchRssFeeds();

        $this->view('blog/index', [
            'pageTitle' => 'Blog',
            'posts' => $posts,
            'veilleArticles' => $veilleArticles,
            'veilleTheme' => 'Sécurisation des API dans les applications web'
        ]);
    }

    public function show($slug = null)
    {
        if (!$slug) {
            $this->redirect('blog');
            return;
        }

        $post = $this->db->fetch("SELECT * FROM posts WHERE slug = ? AND published = 1", [$slug]);

        if (!$post) {
            $this->redirect('blog');
            return;
        }

        $this->view('blog/show', [
            'pageTitle' => $post['title'],
            'post' => $post
        ]);
    }

    /**
     * Récupère les articles depuis les flux RSS
     */
    private function fetchRssFeeds()
    {
        $articles = [];
        $cacheFile = BASE_PATH . '/database/rss_cache.json';
        $cacheTime = 3600; // 1 heure

        // Vérifier si le cache existe et est valide
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile)) < $cacheTime) {
            $cached = json_decode(file_get_contents($cacheFile), true);
            if ($cached) {
                return $cached;
            }
        }

        // Mots-clés pour filtrer les articles pertinents
        $keywords = [
            'api', 'security', 'sécurité', 'authentication', 'authentification',
            'oauth', 'jwt', 'token', 'password', 'mot de passe', 'hash', 'bcrypt',
            'xss', 'csrf', 'sql injection', 'injection', 'vulnerability', 'vulnérabilité',
            'rgpd', 'gdpr', 'data protection', 'encryption', 'chiffrement',
            'ssl', 'tls', 'https', 'certificate', 'certificat', 'web application',
            'firewall', 'waf', 'penetration', 'pentest', 'exploit', 'breach',
            'ransomware', 'malware', 'phishing', 'cyberattack', 'cyberattaque'
        ];

        foreach ($this->rssFeeds as $feed) {
            try {
                $feedArticles = $this->parseRssFeed($feed['url'], $feed['name'], $feed['category'], $keywords);
                $articles = array_merge($articles, $feedArticles);
            } catch (Exception $e) {
                // Silently continue if a feed fails
                continue;
            }
        }

        // Trier par date (plus récent en premier)
        usort($articles, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Limiter à 20 articles
        $articles = array_slice($articles, 0, 20);

        // Sauvegarder dans le cache
        file_put_contents($cacheFile, json_encode($articles));

        return $articles;
    }

    /**
     * Parse un flux RSS
     */
    private function parseRssFeed($url, $sourceName, $category, $keywords)
    {
        $articles = [];
        
        // Configurer le contexte pour les requêtes HTTP
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) Portfolio RSS Reader'
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]);

        $content = @file_get_contents($url, false, $context);
        if (!$content) {
            return $articles;
        }

        // Désactiver les erreurs XML
        libxml_use_internal_errors(true);
        $xml = simplexml_load_string($content);
        
        if (!$xml) {
            return $articles;
        }

        // Parser selon le format (RSS ou Atom)
        $items = [];
        if (isset($xml->channel->item)) {
            // RSS 2.0
            $items = $xml->channel->item;
        } elseif (isset($xml->entry)) {
            // Atom
            $items = $xml->entry;
        }

        $count = 0;
        foreach ($items as $item) {
            if ($count >= 5) break; // Max 5 articles par source

            // Extraire les données selon le format
            if (isset($item->title)) {
                $title = (string)$item->title;
            } else {
                continue;
            }

            $link = '';
            if (isset($item->link)) {
                if (isset($item->link['href'])) {
                    $link = (string)$item->link['href']; // Atom
                } else {
                    $link = (string)$item->link; // RSS
                }
            }

            $description = '';
            if (isset($item->description)) {
                $description = strip_tags((string)$item->description);
            } elseif (isset($item->summary)) {
                $description = strip_tags((string)$item->summary);
            } elseif (isset($item->content)) {
                $description = strip_tags((string)$item->content);
            }
            $description = mb_substr($description, 0, 200) . '...';

            $date = '';
            if (isset($item->pubDate)) {
                $date = (string)$item->pubDate;
            } elseif (isset($item->published)) {
                $date = (string)$item->published;
            } elseif (isset($item->updated)) {
                $date = (string)$item->updated;
            }

            // Vérifier si l'article est pertinent (contient des mots-clés)
            $content = strtolower($title . ' ' . $description);
            $isRelevant = false;
            foreach ($keywords as $keyword) {
                if (strpos($content, strtolower($keyword)) !== false) {
                    $isRelevant = true;
                    break;
                }
            }

            // Ajouter tous les articles du CERT-FR et OWASP (toujours pertinents)
            if (strpos($sourceName, 'CERT') !== false || strpos($sourceName, 'OWASP') !== false) {
                $isRelevant = true;
            }

            if ($isRelevant || $count < 2) { // Au moins 2 articles par source
                $articles[] = [
                    'title' => $title,
                    'link' => $link,
                    'description' => $description,
                    'date' => $date,
                    'source' => $sourceName,
                    'category' => $category
                ];
                $count++;
            }
        }

        return $articles;
    }
}
