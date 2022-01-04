<?php
function toolbar(){
    include_once('components/toolbar.php');
}
function footer(){
    include_once('components/footer.php');
}
function sidebar(){
    include_once('components/sidebar.php');
}

function admin_mail(){
    include_once('config.php');
    return $admin_mail;
}

class DB{
    private static function connect(){
        include('config.php');
        $host = $admin_host;
        $db = $admin_database;
        $user = $admin_user;
        $password = $admin_password;

        $pdo = new PDO('mysql:host='.$host.';dbname='.$db,$user,$password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        return $pdo;
    }

    public static function query($query, $params = array()){
        $stmt = self::connect()->prepare($query);
        $stmt->execute($params);
        if(explode(' ', $query)[0] == 'SELECT'){
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $data;
        }
    }

    // Zwracanie ustawień
    public static function get_settings($name){
        $query = "SELECT * FROM settings WHERE name='".$name."'"; 
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $data[0]['data'];
    }

    // Zwracanie komponentu
    public static function get_component($name){
        $query = "SELECT * FROM components WHERE name='".$name."'"; 
        $stmt = self::connect()->prepare($query);
        $stmt->execute();
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $decoded = json_decode($data[0]['data'],true);
        return $decoded;
    }
}

// Walidacja maila
function email_validation($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }
    // Jezeli poprawny
    else return true;
}

// Zwracnie slug
function slugify($text)
{
  // replace non letter or digits by -
  $text = preg_replace('~[^\pL\d]+~u', '-', $text);

  // transliterate
  $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

  // remove unwanted characters
  $text = preg_replace('~[^-\w]+~', '', $text);

  // trim
  $text = trim($text, '-');

  // remove duplicate -
  $text = preg_replace('~-+~', '-', $text);

  // lowercase
  $text = strtolower($text);

  if (empty($text)) {
    return 'n-a';
  }

  return $text;
}

// Dodawanie strony do bazy
function add_new_post($data){
    // Przygotowywanie sluga
    $slug = slugify($data['title']);
    
    // Przygotowywanie obrazka
    // $img = 'http://free.pagepeeker.com/v2/thumbs.php?size=x&url='.$data['url'];
    $img = 'https://thumbnail.ws/get/thumbnail/?apikey=ab45a17344aa033247137cf2d457fc39abcd7e16a464&url='.$data['url'];

    // Sprawdzanie czy tagi są już dodane do bazy i tworzenie relacji
    $keywords_relations = [];
    $list_of_keywords = explode(",",$data['keywords']);
    foreach($list_of_keywords as $keyword){
        $keyword_slug = slugify($keyword);
        $result = DB::query('SELECT * FROM tags WHERE slug=:keyword_slug',['keyword_slug'=>$keyword_slug]);
        // Jeżeli istnieje taki już keyword (slug) to przypisz relacje do nowej strony
        if(count($result)) {
            $t_id = intval($result[0]['id']);
            array_push($keywords_relations, $t_id);
        }
        else {
            DB::query("INSERT INTO tags (name,slug) VALUES (:name,:slug)",["name"=>$keyword,"slug"=>$keyword_slug]);
            $r_id = DB::query('SELECT * FROM tags WHERE slug=:slug',["slug"=>$keyword_slug]);
            $t_id = intval($r_id[0]['id']);
            array_push($keywords_relations, $t_id);
        }
    }
    $keywords_encoded = json_encode($keywords_relations);

    // linkowanie strony
    $website_url = $data['url'];

    // Tytuł
    $title = $data['title'];

    // Opis
    $description = $data['description'];

    // Polecany / rating i votes / status moda
    $ratings = $data['ratings'];
    $votes = $data['votes'];
    $recommended = $data['recommended'];
    $accepted = $data['accepted'];

    // Email
    $email = $data['email'];

    // Dodawanie strony do bazy
    DB::query('INSERT INTO posts (title,url,slug,img,ratings,votes,description,keywords,recommended,accepted,email) VALUES (:title,:url,:slug,:img,:ratings,:votes,:description,:keywords,:recommended,:accepted,:email)',
        [
            "title"=>$title,
            "url"=>$website_url,
            "slug"=>$slug,
            "img"=>$img,
            "ratings"=>$ratings,
            "votes"=>$votes,
            "description"=>$description,
            "keywords"=>$keywords_encoded,
            "recommended"=>$recommended,
            "accepted"=>$accepted,
            "email"=>$email
        ]
    );
    $catalog_title = DB::get_settings('website_title');

    // Jezeli moderacja jest włączona
    if(DB::get_settings('moderator_mode'))
        $mail_text = 'Dziękujemy za dodanie strony do katalogu. Strona pojawi się na naszej stronie po akcpetacji przez moderatora.';
    else 
        $mail_text = 'Dziękujemy za dodanie strony do katalogu.';
    $send_data = [
        'title'=>'STRONA ZOSTAŁA DODANA DO KATALOGU - '.$catalog_title,
        'text'=>$mail_text
    ];
    send_mail($send_data,$email);
    return 'done';
}


// Wyswietlanie paginacji - paginacja open-source autorstwa jasongrimes/php-paginator
class Paginator
{
    const NUM_PLACEHOLDER = '(:num)';

    protected $totalItems;
    protected $numPages;
    protected $itemsPerPage;
    protected $currentPage;
    protected $urlPattern;
    protected $maxPagesToShow = 10;
    protected $previousText = 'Poprzednia';
    protected $nextText = 'Następna';

    /**
     * @param int $totalItems The total number of items.
     * @param int $itemsPerPage The number of items per page.
     * @param int $currentPage The current page number.
     * @param string $urlPattern A URL for each page, with (:num) as a placeholder for the page number. Ex. '/foo/page/(:num)'
     */
    public function __construct($totalItems, $itemsPerPage, $currentPage, $urlPattern = '')
    {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = $currentPage;
        $this->urlPattern = $urlPattern;

        $this->updateNumPages();
    }

    protected function updateNumPages()
    {
        $this->numPages = ($this->itemsPerPage == 0 ? 0 : (int) ceil($this->totalItems/$this->itemsPerPage));
    }

    /**
     * @param int $maxPagesToShow
     * @throws \InvalidArgumentException if $maxPagesToShow is less than 3.
     */
    public function setMaxPagesToShow($maxPagesToShow)
    {
        if ($maxPagesToShow < 3) {
            throw new \InvalidArgumentException('maxPagesToShow cannot be less than 3.');
        }
        $this->maxPagesToShow = $maxPagesToShow;
    }

    /**
     * @return int
     */
    public function getMaxPagesToShow()
    {
        return $this->maxPagesToShow;
    }

    /**
     * @param int $currentPage
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = $currentPage;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    /**
     * @param int $itemsPerPage
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = $itemsPerPage;
        $this->updateNumPages();
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }

    /**
     * @param int $totalItems
     */
    public function setTotalItems($totalItems)
    {
        $this->totalItems = $totalItems;
        $this->updateNumPages();
    }

    /**
     * @return int
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }

    /**
     * @return int
     */
    public function getNumPages()
    {
        return $this->numPages;
    }

    /**
     * @param string $urlPattern
     */
    public function setUrlPattern($urlPattern)
    {
        $this->urlPattern = $urlPattern;
    }

    /**
     * @return string
     */
    public function getUrlPattern()
    {
        return $this->urlPattern;
    }

    /**
     * @param int $pageNum
     * @return string
     */
    public function getPageUrl($pageNum)
    {
        return str_replace(self::NUM_PLACEHOLDER, $pageNum, $this->urlPattern);
    }

    public function getNextPage()
    {
        if ($this->currentPage < $this->numPages) {
            return $this->currentPage + 1;
        }

        return null;
    }

    public function getPrevPage()
    {
        if ($this->currentPage > 1) {
            return $this->currentPage - 1;
        }

        return null;
    }

    public function getNextUrl()
    {
        if (!$this->getNextPage()) {
            return null;
        }

        return $this->getPageUrl($this->getNextPage());
    }

    /**
     * @return string|null
     */
    public function getPrevUrl()
    {
        if (!$this->getPrevPage()) {
            return null;
        }

        return $this->getPageUrl($this->getPrevPage());
    }

    /**
     * Get an array of paginated page data.
     *
     * Example:
     * array(
     *     array ('num' => 1,     'url' => '/example/page/1',  'isCurrent' => false),
     *     array ('num' => '...', 'url' => NULL,               'isCurrent' => false),
     *     array ('num' => 3,     'url' => '/example/page/3',  'isCurrent' => false),
     *     array ('num' => 4,     'url' => '/example/page/4',  'isCurrent' => true ),
     *     array ('num' => 5,     'url' => '/example/page/5',  'isCurrent' => false),
     *     array ('num' => '...', 'url' => NULL,               'isCurrent' => false),
     *     array ('num' => 10,    'url' => '/example/page/10', 'isCurrent' => false),
     * )
     *
     * @return array
     */
    public function getPages()
    {
        $pages = array();

        if ($this->numPages <= 1) {
            return array();
        }

        if ($this->numPages <= $this->maxPagesToShow) {
            for ($i = 1; $i <= $this->numPages; $i++) {
                $pages[] = $this->createPage($i, $i == $this->currentPage);
            }
        } else {

            // Determine the sliding range, centered around the current page.
            $numAdjacents = (int) floor(($this->maxPagesToShow - 3) / 2);

            if ($this->currentPage + $numAdjacents > $this->numPages) {
                $slidingStart = $this->numPages - $this->maxPagesToShow + 2;
            } else {
                $slidingStart = $this->currentPage - $numAdjacents;
            }
            if ($slidingStart < 2) $slidingStart = 2;

            $slidingEnd = $slidingStart + $this->maxPagesToShow - 3;
            if ($slidingEnd >= $this->numPages) $slidingEnd = $this->numPages - 1;

            // Build the list of pages.
            $pages[] = $this->createPage(1, $this->currentPage == 1);
            if ($slidingStart > 2) {
                $pages[] = $this->createPageEllipsis();
            }
            for ($i = $slidingStart; $i <= $slidingEnd; $i++) {
                $pages[] = $this->createPage($i, $i == $this->currentPage);
            }
            if ($slidingEnd < $this->numPages - 1) {
                $pages[] = $this->createPageEllipsis();
            }
            $pages[] = $this->createPage($this->numPages, $this->currentPage == $this->numPages);
        }


        return $pages;
    }


    /**
     * Create a page data structure.
     *
     * @param int $pageNum
     * @param bool $isCurrent
     * @return Array
     */
    protected function createPage($pageNum, $isCurrent = false)
    {
        return array(
            'num' => $pageNum,
            'url' => $this->getPageUrl($pageNum),
            'isCurrent' => $isCurrent,
        );
    }

    /**
     * @return array
     */
    protected function createPageEllipsis()
    {
        return array(
            'num' => '...',
            'url' => null,
            'isCurrent' => false,
        );
    }

    /**
     * Render an HTML pagination control.
     *
     * @return string
     */
    public function toHtml()
    {
        if ($this->numPages <= 1) {
            return '';
        }

        $html = '<ul class="pagination">';
        if ($this->getPrevUrl()) {
            $html .= '<li><a href="' . htmlspecialchars($this->getPrevUrl()) . '">&laquo; '. $this->previousText .'</a></li>';
        }

        foreach ($this->getPages() as $page) {
            if ($page['url']) {
                $html .= '<li' . ($page['isCurrent'] ? ' class="active"' : '') . '><a href="' . htmlspecialchars($page['url']) . '">' . htmlspecialchars($page['num']) . '</a></li>';
            } else {
                $html .= '<li class="disabled"><span>' . htmlspecialchars($page['num']) . '</span></li>';
            }
        }

        if ($this->getNextUrl()) {
            $html .= '<li><a href="' . htmlspecialchars($this->getNextUrl()) . '">'. $this->nextText .' &raquo;</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }

    public function __toString()
    {
        return $this->toHtml();
    }

    public function getCurrentPageFirstItem()
    {
        $first = ($this->currentPage - 1) * $this->itemsPerPage + 1;

        if ($first > $this->totalItems) {
            return null;
        }

        return $first;
    }

    public function getCurrentPageLastItem()
    {
        $first = $this->getCurrentPageFirstItem();
        if ($first === null) {
            return null;
        }

        $last = $first + $this->itemsPerPage - 1;
        if ($last > $this->totalItems) {
            return $this->totalItems;
        }

        return $last;
    }

    public function setPreviousText($text)
    {
        $this->previousText = $text;
        return $this;
    }

    public function setNextText($text)
    {
        $this->nextText = $text;
        return $this;
    }
}

function send_mail($data,$email){
    $to      = $email; // odbiorca
    $subject = $data['title'];
    $message = $data['text'];
    $headers = 'From: katalog@'.$_SERVER['HTTP_HOST'] . "\r\n" . // Nadawaca
        'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $message, $headers);
}


// Site map generator
function sitemap_generator(){
    $sitemap_file = fopen("sitemap.xml", "w");
    $sitemap_start = '<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
	xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
        http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">';
	fwrite($sitemap_file, $sitemap_start);

	// Czy serwis jest postawiony na https
	if(isset($_SERVER['HTTPS'])) $enc = 'https://';
	else $enc = 'http://';

	$date = gmdate("Y-m-d");
	$time = gmdate("H:i:s");
	$prepared_time = $date.'T'.$time.'+00:00';

	// Dodawanie strony głównej
	$sitemap_text = '
	<url>
		<loc>'.$enc.$_SERVER['HTTP_HOST'].'</loc>
		<lastmod>'.$prepared_time.'</lastmod>
		<priority>1.00</priority>
	</url>';

	fwrite($sitemap_file, $sitemap_text);
	// Dodawanie stron pobocznych, systemowych
	$sitemap_text = '
	<url>
		<loc>'.$enc.$_SERVER['HTTP_HOST'].'/strony</loc>
		<lastmod>'.$prepared_time.'</lastmod>
		<priority>0.80</priority>
	</url>
	<url>
		<loc>'.$enc.$_SERVER['HTTP_HOST'].'/tagi</loc>
		<lastmod>'.$prepared_time.'</lastmod>
		<priority>0.80</priority>
	</url>
	<url>
		<loc>'.$enc.$_SERVER['HTTP_HOST'].'/polecamy</loc>
		<lastmod>'.$prepared_time.'</lastmod>
		<priority>0.80</priority>
	</url>
	<url>
		<loc>'.$enc.$_SERVER['HTTP_HOST'].'/regulamin</loc>
		<lastmod>'.$prepared_time.'</lastmod>
		<priority>0.80</priority>
	</url>
	<url>
		<loc>'.$enc.$_SERVER['HTTP_HOST'].'/kontakt</loc>
		<lastmod>'.$prepared_time.'</lastmod>
		<priority>0.80</priority>
	</url>';

	fwrite($sitemap_file, $sitemap_text);

	// Dodawanie postów
	$all_posts = DB::query('SELECT * FROM posts');

	$sitemap_text = '';
	foreach($all_posts as $post) {
	$sitemap_text .= '
	<url>
		<loc>'.$enc.$_SERVER['HTTP_HOST'].'/strona/'.$post['id'].'/'.$post['slug'].'</loc>
		<lastmod>'.$prepared_time.'</lastmod>
		<priority>0.80</priority>
	</url>';
	}
	fwrite($sitemap_file, $sitemap_text);

	// Dodawanie tagów
	$all_tags = DB::query('SELECT * FROM tags');

	$sitemap_text = '';
	foreach($all_tags as $tag) {
	$sitemap_text .= '
	<url>
		<loc>'.$enc.$_SERVER['HTTP_HOST'].'/tag/'.$tag['slug'].'</loc>
		<lastmod>'.$prepared_time.'</lastmod>
		<priority>0.50</priority>
	</url>';
	}
	fwrite($sitemap_file, $sitemap_text);

	// Zamykanie sitemap
	$sitemap_end = '
</urlset>';
fwrite($sitemap_file, $sitemap_end);

fclose($sitemap_file);
}