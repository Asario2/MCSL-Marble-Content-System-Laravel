<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    class Settings extends Model
    {
        // Definiere hier deine Konstanten oder statischen Variablen
        public static array $excl_cols = ['updated_at', 'published_at','remember_token','email_verified_at','google2fa_secret','is_two_factor_authenticated','two_factor_authenticated',
                        'two_factor_enabled',"password_old",'reserved_at','uhash','available_at','is_two_factor_enabled','temp_google2fa_secret','new_email','email_change_token','email_verification_token','two_factor_token',"author_name","users_rights_id","chg_date","xico_doms",'xkis_IsFeed','xis_mailed','users_rights_id','two_factor_secret','two_factor_recovery_codes','two_factor_confirmed_at','two_factor_enabled','remember_token',
                        'email_hash','hasyear',"hasryear",
                    ];
        public static array $excl_heads = ["date_begin"];
        public static array $excl_disabled = ['id'];
        public static array $excl_datefields  = []; //['birthday','created_at'];
            public static array $exl = [
        'text' => 'Text',
        'title' => 'Titel',
        'content_en' => 'Text Englisch',
        'blog_categories_id' => 'Kategorie',
        'title_en' => 'Titel Englisch',
        'users_id' => 'Autor',
        'moderator_id' => 'Moderator',
        'image_categories_id' => 'Bilder Kategorie',
        'to_address' => 'Empfängeradresse',
        'subject' => 'Betreff',
        'pub' => 'Öffentlich',
        'view_table' => 'Anzeigen',
        'add_table' => 'Hinzufügen',
        'publish_table' => 'Veröffentlichen',
        'date_table' => 'Datum Ändern',
        'delete_table' => 'Löschen',
        'edit_table' => 'Bearbeiten',
        'content' => 'Text',
        'xis_ai' => 'KI Bild',
        'summary' => 'Zusammenfassung',
        'blog_authors_id' => 'Autor',
        'date_end' => 'Online Von/Bis',
        'post_id' => 'Post ID',
        'categories_id' => 'Kategorie',
        'xis_IsSaleable' => 'Verkäuflich',
        'xkis_Ticker' => 'Ticker Aktivieren',
        'created_at' => 'Erstellt am:',
        'headline' => 'Überschrift',
        'headline_en' => 'Überschrift Englisch',
        'answer' => 'Antwort',
        'date' => 'Datum',
        'birthday' => 'Alter',
        'occupation' => 'Beschäftigung',
        'interests' => 'Hobbys',
        'music' => 'Musik',
        'prename' => 'Vorname',
        'id' => 'ID',
        'image_path' => 'Bild',
        'email' => 'Email',
        'name' => 'Name',
        'xis_aiImage' => 'Bild mit KI erstellt',
        'blog_date' => 'Öffentlich ab:',
        'slug' => 'URL',
        'reading_time' => 'Lesezeit',
        'markdown_on' => 'Markdown verwendet',
        'blog_images_iid' => 'Bildupload',
        'updated_at' => 'Letzte Änderung',
        'first_name' => 'Vorname',
        'email_verified_at' => 'Email bestätigt',
        'password' => 'Passwort',
        'users_rights_id' => 'Benutzergruppe',
        'profile_photo_path' => 'Profilbild',
        'is_admin' => 'Administrator',
        'is_employee' => 'Mitarbeiter',
        'is_customer' => 'Kunde',
        'admin_id' => 'Admin ID',
        'company_id' => 'Firmen ID',
        'customer_id' => 'Kunden ID',
        'last_login_at' => 'Letzter Login',
        'two_factor_secret' => '2fa Secret',
        'two_factor_recovery_codes' => '2fa Recovery Codes',
        'two_factor_confirmed_at' => '2fa bestätigt am',
        'remember_token' => 'Login merken',
        'position' => 'Position',
        'shortname' => 'Kurzname',
        'xkis_AdminPanel' => 'AdminPanel Zugriff',
        'xkis_UserRights' => 'Benutzergruppen Zugriff',
        'xkis_SendMail' => 'Email Senden',
        'xkis_SandMail' => 'Email Senden',
        'xkis_CommentsCenter' => 'Commentscenter',
        'xkis_comment_edit' => 'Kommentarbearbeitung',
        'Kommentarbearbeitung' => 'Alle Felder bearbeiten',
        'author_id' => 'Autor',
        'chg_date' => 'Geändert am:',
        'description' => 'Beschreibung',
        'modul' => 'Modul',
        'published_at' => 'Veröffentlicht am:',
        'name_en' => 'Name Englisch',
        'heading_alt' => 'Überschrift',
        'heading_alt_en' => 'Überschrift Englisch',
        'img_icon' => 'Icon',
        'AscName' => 'AscName',
        'AscName_en' => 'AscName Englisch',
        'description_en' => 'Beschreibung Englisch',
        'itemscope' => 'Objekttyp',
        'xis_shopable' => 'Verkäuflich',
        'status' => 'Status',
        'message' => 'Text',
        'message_en' => 'Text Englisch',
        'camera_id' => 'Kamera',
        'link' => 'Link',
        'Format' => 'Format',
        'Format_en' => 'Format Englisch',
        'preis' => 'Preis',
        'tablename' => 'Tabelle',
        'xis_checked' => 'checked',
        'ordering' => 'Position',
        'Longname' => 'Langer Name',
        'Mpixel' => 'Megapixel',
        'story_en' => 'Geschichte Englisch',
        'story' => 'Geschichte',
        'short_tag' => 'Tag',
        'exif_copyright' => 'Exif copy',
        'PrivateMessages' => 'Private Nachrichten',
        'autoslug' => 'Url der Seite',
        'admin_table_id' => 'Tabelle',
        'exif_comp' => 'exif Comp',
        'exif_model' => 'Exif Model',
        'AdminPanel' => 'Admin Dashboard',
        'UserRights' => 'BenutzerRechte',
        'LogViewer' => 'Log Viewer',
        'SendMail' => 'Emails verschicken',
        'ChangePassword' => 'Passwort Ändern',
        'CommentsCenter' => 'Kommentar Admin Center',
        'DataBases' => 'Datenbank Admin',
        'about' => 'Über dich',
        'ischecked' => 'Überprüft',
        'projects_id' => 'Aktuelles Projekt',
        'xis_mcsl' => 'mit MCSL erstellt',
        'xis_mcs' => 'mit MCS erstellt',
        'about_en' => 'Über dich Englisch',
        'website' => 'Website',
        'fbd' => 'facebook ID',
        'xis_disabled' => 'Benutzer deaktivieren',
        'CommentsEdit' => 'Alle Felder bearbeiten',
        'wohnort' => 'Wohnort',
        'realname' => 'Realname',
        'aufgaben' => 'Aufgaben',
        'abouttext' => 'Über User',
        'img_thumb' => 'Bild klein',
        'img_big' => 'Bild gross',
        'imgdir_content' => 'Galerie',
        'xis_public_con' => 'Für alle Benutzer sichtbar',
        'ripdate' => 'Todesdatum',
        'us_poster' => 'Author',
        'mail_body' => 'Text',
        'xis_anzeige' => 'Anzeige',
        'Contacts' => 'Kontakte',
        'SendMailToAll' => 'An alle Nutzer senden',
        'Statistics' => 'Zugriffsstatistiken',
        'StatisticsAll' => 'Statistiken alle Domains',
        'UserDisable' => 'Benutzer deaktivieren',
        'SQLUpdate' => 'Datenbank Synchronisieren',
        'test dzads' => 'asdasd',
        'UnusedImages' => 'Unbenutzte Bilder Galerie',
    ];
    public static array $regdom =   ['ab'=>true];
    public static array $connect_dbname = ["ab"=>"mariadb","dag"=>"mariadb_dag","mfx"=>"mariadb_mfx"];

    /*

    SQLUPDATE EXCUDED

    */

    public static array $excl_dump_tables = ["xgen_migrations","mcs_backup_doku","mcsdoku2","password_reset_tokens","personal_access_tokens","blog_images","private_messages","xgen_page_views","xgen_migrations","privacy_orig","colors","dbhash","tenant","cache","cache_locks","comments","countpixel","newsletter_blacklist","newsletter_reci","prvacy_orig","privaze_messages","private_messages_text","ratings","sessions"];

        public static array $no_req = ['exif_copyright','exif_comp','exif_model','Mpixel','modul','is_admin','is_customer','is_employee','customer_id','admin_id','company_id',
                                       'profile_photo_path',"category_id","type_id",'message','message_en',
                                       'position','ordering','image_path','link','format','preis','format_en','music','interest',"about",'story_en',
                                       'img_bild',"img_thumb",'occupation','name_res','desc_res','new_res','birthday','answer_en','prename','xis_ai','typ',"about",'id_new','exif_comp','exif_model',"interests","occupation","fbd","website","about_en","about"
                                       ,'db_name','db_new','Format','db_desc','db_link','db_icon',"xis_public_con","Kommentar","Telefon","Handy","Strasse","Plz","Geburtsdatum","ripdate","xis_public_con","Email","Nachname","Vorname",
                                    ];



        public static array $big_thumb = ["users","blog_posts","images"];
        public static array $int_date_tables = ["didyouknow"];
        public static array $textfield = ["HTML"]; // Mdown / HTML
        public static array $thirdparty = ["blog_authors"=>"Info"];
        public static array $userexcl = ["contacts"=>"/admin/Kontakte"];
        public static function exclWhere(): array
        {
            return [
                'contacts' => [
                    [
                        'name'  => 'us_poster',
                        'value' => Auth::id(),
                        'type'  => 'where',
                    ],
                    [
                        'name'  => 'xis_public_con',
                        'value' => 1,
                        'type'  => 'orWhere',
                    ],
                ],
            ];
        }

        public static array $disa = [
            'comments' => ['users_id','post_id', 'admin_table_id', 'nick', 'email'],
        ];

        public static array $impath = ["users"=>'profile_photo_path','default'=>"image_path"];

        public static array $dom = ["ab"=>'www.asario.de','mfx'=>"www.marblefx.net",'dag'=>"www.monikadargies.de",'chh'=>"www.ra-c-henning.de",'mjs'=>"mjs.marblefx.net"];

        public static array $headline =
        [
            "admin_table" => 'name',
            "blogs" => 'title',
            "contacts" => "Gruppe",
            "blog_authors" => 'name',
            "blog_categories" => 'name',
            "blog_images" => 'name',
            "images"=>'name',
            "comments" => 'content',
            "image_categories" => 'name',
            "didyouknow" => 'headline',
            "users" => 'name',
            "infos" => 'headline',
            "projects"=>"name",
            "sprueche"=>"author",
            "users_rights" => 'name',
            "projects_sheets"=>"aufgabe",
            "camera"=>"name",
            "testfield"=>"title",
            "shortpoems"=>"headline",
            "privacy"=>"headline",
            "texts"=>"headline",
            'news'=>"headline",
            "people"=>"name",
            "ratings"=>"table",
            "kontakt"=>"name",
            "lostnfound"=>"headline",
            "links"=>"headline",
            "categories"=>"name",
            "types"=>"name",
            "users_rights"=>"name",
            "private_messages"=>"subject",
            ];
    public static array $searchable = [
        'ab' => ["images","blogs","didyouknow","shortpoems","users"],
        'mfx' => ['images',"people","projects"],
    ];
    public static array $statusvals  = ["empty"=>"keine Angabe","forsale"=>"Zu Verkaufen","givenaway"=>"Verschenkt","sold"=>"Verkauft","unsaleable"=>"Unverkäuflich","lost"=>"Verloren","inwork"=>"In Arbeit"];
    public static array $searchFields =
        [
            "admin_table" => ['name'],
            "blogs" => ['title','content'/*'content_en','title_en'*/,"blog_categories.name"],
            "blog_authors" => ['name',"Info"],
            "blog_categories" => ['name',"summary"],
            "blog_images" => ['name'],
            "types" => ['name,"name_en'],
            "images"=> ['name',"message","headline_en","message_en"],
            "comments" => ['users.name','content',"email","admin_table.name"],
            "didyouknow" => ['headline',"answer"],
            "image_categories"=>['heading_alt'],
            "users" => ['name',"email"],
            "projects_sheets"=>["headline","users.name","projects.name"],
            "projects"=>['headline',"Umfang"],
            "users_rights" => ['name'],
            "camera" => ['name','Longname',"Mpixel"],
            "testfield" => ['title'.'content'],
            "infos"=>["headline","message","summary"],
            "shortpoems" => ['headline','story'],
            "texts"=>["headline","text"],
            "news" =>['headline','message'],
            "privacy"=>["headline","message"],
            "spruche"=>["author","text"],
            "people" =>['name',"abouttext",'realname','aufgaben','wohnort','website',"email"],
            "ratings"=>['table',"images.name"],
            "kontakt"=>["name","email","strasse","plz","tel"],
            "lostnfound"=>["headline","message"],
            "links"=>["headline","url","messsage"],
            "contacts"=>["Name","Gruppe"],
            "categories"=>["name","name_en"],
            "private_messages"=>["subject"],
            "users_rights"=>["name","shortname"],
    ];
    public static array $otherField = [
        'admin_table'=> "description",
        'blog_authors'=> 'info',
        'blogs'=> 'summary',
        "blog_categories" => "summary",
        "image_categories"=>'heading_alt',
        "images"=> "message",
        "didyouknow"=>"answer",
        "private_messages"=>"subject",
        'infos'=>"message",
        "camera"=> "Longname",
        'testfield' => "content",
        'comments' => 'users_id',
        'projects_sheets' => 'projects_id',
        "privacy" => 'message',
        "shortpoems"    => "story",
        "camera"=>"LongName",
        "texts"=>"text",
        "users"=>"email",
        "news"=>"message",
        "projects"=>'Umfang',
        "people"=>"abouttext",
        "ratings"=>"rating",
        "kontakt"=>"email",
        "lostnfound"=>"message",
        "links"=>"url",
        "contacts"=>'Name',
        "categories"=>'name_en',
        "sprueche"=>"text",
        "types" => 'name_en',
        "users_rights"=>"shortname"
    ];
    public static array $namealias = [
        "comments"=>"Kommentar",
        "ratings"=>"Galerie",
        "projects_sheets"=>"Projekt",
        "lostnfound"=>"Überschrift",
        "contacts"=>"Gruppe",
        "sprueche"=>"Author",
        "blog_authors"=>"Author",
        "categories"=>"Kategorie",
        "users_rights"=>"Name",
        "blogs"=>"Headline",
    ];
    public static array $descalias = [
        "comments"=>"Autor",
        "blog_authors"=>"Benutzer",
        "ratings"=>"Benutzer",
        "users"=>"E-Mail",
        "shortpoems"=>"Autor",
        "didyouknow"=>"Autor",
        "texts"=>"Autor",
        "blogs"=>"Zusammenfassung",
        "categories"=>"Kategorie Englsich",
        "images"=>"Autor",
        "projects_sheets"=>"Aufgabe",
        "kontakt"=>"Email",
        "lostnfound"=>"Text",
        "sprueche"=>"Text",
        "links"=>"URL",
        'contacts'=>"Name",
        'users_rights'=>"Kurzname",
    ];
    public static array $underCals=[
    'comments' => "name",

    ];
    public static array $presetting = [
        "blogs"=>"blog_categories",
        "images"=>"images",
        "comments"=>"users",
        // "ratings"=>"users",
        "project_sheets"=>"projects",
    ];
    public static array $aftsetting = [
        "blogs"=>"author",
    ];

public static array $image_sizes =
        [
            'blog_posts' => [
                'big' => 1205,
                'thumbs' => 350,
                'default' => 700
            ],
            'default' => [
                'big' => 1205,
                'thumbs' => 350,
                'default' => 700
            ],
            'images' => [
                'big' => 5200,
                'thumbs' => 450,
                'default' => 2440
            ]
        ];
        public static array $image_paths = ["blogs"=>"blogs"];
        public static array $copyright_tables = ["images"];
        public static array $copyright_marker =
        [
            "asario"=>[
                "images"=>"image_path",

            ],
            "liri"=>[
                "images"=>"image_path"
            ],
            "rob"=>
            [
                "images"=>"image_path"
            ],
            "iano"=>
            [
                "images"=>"image_path"
            ],
            "nitro"=>
            [
                "images"=>"image_path"
            ],
            "mazi"=>
            [
                "images"=>"image_path"
            ]

        ];
    public static array $Ignored_Field = [
    0 => 'ab_users_rights_xkis_SQLUpdate',
    1 => 'dag_users_rights_xkis_SQLUpdate',
    2 => 'mfx_users_rights_xkis_SQLUpdate',
    3 => 'ab_users_rights_updated_at',
    4 => 'dag_users_rights_updated_at',
    5 => 'mfx_users_rights_updated_at',
    6 => 'ab_users_last_login_at',
    7 => 'ab_users_updated_at',
    8 => 'ab_users_remember_token',
];
public static array $doms = [
    "ab_lh"=>"http://ab.localhost.de",
    "mfx_lh"=>"http://mfx.localhost.de",
    "dag_lh"=>"http://mfx.localhost.de",
    "ab_ol"=>"https://www.asario.de",
    "mfx_ol"=>"https://www.marblefx.de",
    "dag_ol"=>"https://www.monikadargies.de",
];
public static array $mariaDBs = [
    "ab"=>"mariadb",
    "dag"=>"mariadb_dag",
    "mfx"=>"mariadb_mfx",
];
    public static array $nostats = [
    '/admin',
    '/_debug',
    '/api/',
    '/login',
    "/mail/",
    '/pm/index',
    'http:',
    'https:',
    ];
    }

