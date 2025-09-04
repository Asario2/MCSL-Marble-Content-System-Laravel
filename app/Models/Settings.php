<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    class Settings extends Model
    {
        // Definiere hier deine Konstanten oder statischen Variablen
        public static array $excl_cols = ['updated_at', 'published_at','remember_token','email_verified_at','google2fa_secret','is_two_factor_authenticated','two_factor_authenticated',
                        'two_factor_enabled',"password_old",'reserved_at','available_at','is_two_factor_enabled','temp_google2fa_secret','new_email','email_change_token','email_verification_token','two_factor_token',"author_name","users_rights_id","chg_date","xico_doms",'xkis_IsFeed','xis_mailed','users_rights_id','two_factor_secret','two_factor_recovery_codes','two_factor_confirmed_at','two_factor_enabled','remember_token'];
        public static array $excl_heads = ["date_begin"];
        public static array $excl_disabled = ['id'];
        public static array $excl_datefields  = []; //['birthday','created_at'];
        public static array $exl = ["text"=>"Text","title"=>"Titel","content_en"=>"Text Englisch","blog_categories_id"=>"Kategorie",'title_en'=>"Titel Englisch",'users_id'=>"Autor","moderator_id"=>"Moderator","image_categories_id"=>"Bildergruppe","to_address"=>'Empfängeradresse','subject'=>'Betreff','pub'=>"Öffentlich",
                    'view_table'=>'Sichtbar','add_table'=>'Hinzufügen','publish_table'=>'Veröffentlichen','date_table'=>"Datum Ändern",'delete_table'=>"Löschen",'edit_table'=>"Editieren","content"=>"Text","xis_ai"=>"KI Bild","summary"=>"Zusammenfassung",
                    "blog_authors_id"=>"Autor",'date_end'=>"Online Von/Bis",'post_id'=>"Post ID",'categories_id'=>"Kategorie",'xis_IsSaleable'=>"Verkäuflich",'xkis_Ticker'=>"Ticker Aktivieren","created_at"=>'Erstellt am:','headline'=>'Überschrift','headline_en'=>'Überschrift Englisch',
                    "answer"=>"Antwort","date"=>"Datum",'birthday' => 'Alter','occupation'=>'Beschäftigung',"interests"=>"Hobbys","music"=>'Musik','prename'=>"Vorname",'id'=>'ID','image_path'=>"Bild","email"=>'Email',
                    'name'=>"Name",'xis_aiImage'=>"Bild mit KI erstellt","blog_date" => "Öffentlich ab:","slug" => "URL","reading_time" => "Lesezeit","markdown_on" => "Markdown verwendet","blog_images_iid" => "Bildupload","updated_at" => "Letzte Änderung","first_name" => "Vorname","name" => "Nickame","email_verified_at" => "Email bestätigt","password" =>
    "Passwort","users_rights_id" => "Benutzergruppe","profile_photo_path" => "Profilbild","is_admin" => "Administrator","is_employee" => "Mitarbeiter","is_customer" => "Kunde","admin_id" => "Admin ID","company_id" => "Firmen ID","customer_id" => "Kunden ID","last_login_at" =>
    "Letzter Login","two_factor_secret" => "2fa Secret","two_factor_recovery_codes" => "2fa Recovery Codes","two_factor_confirmed_at" => "2fa bestätigt am","remember_token" => "Login merken","position" => "Position","shortname" => "Kurzname","xkis_AdminPanel" => "AdminPanel Zugriff","xkis_UserRights" =>
    "Benutzergruppen Zugriff","xkis_SendMail" => "Email Senden","xkis_SandMail" => "Email Senden","xkis_CommentsCenter" => "Commentscenter","xkis_comment_edit"=>"Kommentarbearbeitung","Kommentarbearbeitung"=>"Alle Felder bearbeiten","author_id" => "Autor","chg_date" => "Geändert am:","name" => "Name","description" => "Beschreibung","modul" => "Modul","published_at" => "Veröffentlicht am:","name_en" =>
    "Name Englisch","heading_alt" => "Überschrift","heading_alt_en" => "Überschrift Englisch","img_icon" => "Icon","AscName" => "AscName","AscName_en" => "AscName Englisch","description_en" => "Beschreibung Englisch","itemscope" => "Objekttyp","xis_shopable" => "Verkäuflich","image_categories_id" => "Bilder Kategorie","status" =>
    "Status","message" => "Text","message_en" => "Text Englisch","camera_id" => "Kamera","link" => "Link","Format" => "Format","Format_en" => "Format Englisch","preis" => "Preis","tablename" => "Tabelle","xis_checked" => "checked","ordering" => "Position","Longname" => "Langer Name","Mpixel" =>
    "Megapixel","view_table"=>"Anzeigen",'add_table'=>"Hinzufügen",'story_en'=>"Geschichte Englisch",'story'=>"Geschichte","edit_table"=>"Bearbeiten","publish_table"=>"Veröffentlichen","date_table"=>"Datum Ändern","delete_table"=>"Löschen","short_tag" => "Tag","exif_copyright" => "Exif copy",
    'autoslug'=>"Url der Seite","admin_table_id"=>"Tabelle","exif_comp" => "exif Comp","exif_model" => "Exif Model","AdminPanel"=>"Admin Dashboard","UserRights"=>"BenutzerRechte",'LogViewer'=>"Log Viewer","SendMail"=>"Emails verschicken","ChangePassword"=>"Passwort Ändern","CommentsCenter"=>"Kommentar Admin Center","DataBases"=>"Datenbank Admin"
    ,"about"=>"Über dich","ischecked"=>"Überprüft",'projects_id'=>'Aktuelles Projekt',"xis_mcsl"=>"mit MCSL erstellt","xis_mcs"=>"mit MCS erstellt","about_en"=>"Über dich Englisch","website" => "Website","fbd"=>"facebook ID","xis_disabled"=>"Benutzer deaktivieren","CommentsEdit"=>"Alle Felder bearbeiten",
    'wohnort'=>"Wohnort","realname"=>"Realname","aufgaben"=>"Aufgaben",'abouttext'=>"Über User","img_thumb"=>"Bild klein","img_big"=>"Bild gross","imgdir_content"=>"Galerie"];

        public static array $no_req = ['exif_copyright','exif_comp','exif_model','Mpixel','modul','is_admin','is_customer','is_employee','customer_id','admin_id','company_id',
                                       'profile_photo_path',"category_id","type_id",'message','message_en',
                                       'position','ordering','image_path','link','format','preis','format_en','music','interest',"about",'story_en',
                                       'img_bild',"img_thumb",'occupation','birthday','answer_en','prename','xis_ai','typ','id_new','exif_comp','exif_model',"interests","occupation","fbd","website","about_en","about"];



        public static array $big_thumb = ["users","blog_posts","images"];
        public static array $int_date_tables = ["didyouknow"];
        public static array $textfield = ["Mdown"]; // Mdown / HTML

        public static array $disa = [
            'comments' => ['users_id','post_id', 'admin_table_id', 'nick', 'email'],
        ];

        public static array $impath = ["users"=>'profile_photo_path','default'=>"image_path"];

        public static array $headline =
        [
            "admin_table" => 'name',
            "blogs" => 'title',
            "blog_authors" => 'name',
            "blog_categories" => 'name',
            "blog_images" => 'name',
            "images"=>'headline',
            "comments" => 'content',
            "image_categories" => 'name',
            "didyouknow" => 'headline',
            "users" => 'name',
            "infos" => 'headline',
            "projects"=>"name",
            "users_rights" => 'name',
            "projects_sheets"=>"aufgabe",
            "camera"=>"name",
            "testfield"=>"title",
            "shortpoems"=>"headline",
            "privacy"=>"headline",
            "texts"=>"headline",
            'news'=>"headline",
            "people"=>"name"

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
            "blog_authors" => ['name'],
            "blog_categories" => ['name',"summary"],
            "blog_images" => ['name'],
            "images"=> ['headline',"message"],
            "comments" => ['users.name','content',"email","admin_table.name"],
            "didyouknow" => ['headline',"answer"],
            "image_categories"=>['heading_alt'],
            "users" => ['name',"email"],
            "projects_sheets"=>["headline","users.name"],
            "projects"=>['headline',"Umfang"],
            "users_rights" => ['name'],
            "camera" => ['name','Longname',"Mpixel"],
            "testfield" => ['title'.'content'],
            "infos"=>["headline","message","summary"],
            "shortpoems" => ['headline','story'],
            "texts"=>["headline","text"],
            "news" =>['headline','message'],
            "privacy"=>["headline","message"],
            "people" =>['name',"abouttext",'realname','aufgaben','wohnort','website',"email"],

    ];
    public static array $otherField = [
        'admin_table'=> "description",
        'blog_images'=> 'url',
        'blogs'=> 'summary',
        "blog_categories" => "summary",
        "image_categories"=>'heading_alt',
        "images"=> "message",
        "didyouknow"=>"answer",
        'infos'=>"message",
        "camera"=> "Longname",
        'testfield' => "content",
        'comments' => 'users_id',
        'projects_sheets' => 'users_id',
        "privacy" => 'message',
        "shortpoems"    => "story",
        "camera"=>"LongName",
        "texts"=>"text",
        "users"=>"email",
        "news"=>"message",
        "projects"=>'Umfang',
        "people"=>"abouttext",
    ];
    public static array $namealias = [
        "comments"=>"Kommentar"
    ];
    public static array $descalias = [
        "comments"=>"Autor",
        "users"=>"E-Mail",
    ];
    public static array $underCals=[
    'comments' => "name",
    ];
    public static array $presetting = [
        "blogs"=>"blog_categories",
        "images"=>"images",
        "comments"=>"users",

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
    }
