<?php
ob_start();
define('API_KEY', '7338047986:AAFWEnvZNFv0ICOGbeWoqGU-B9TJu5JhL9U');

function bot($method, $datas = []) {
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $datas);
    $res = curl_exec($ch);
    if (curl_error($ch)) {
        var_dump(curl_error($ch));
    } else {
        return json_decode($res);
    }
}



function getAdhkar() {
    $url1 = "https://yank7589.serv00.net/Visco/adhkari1.php?k=";
    $response = file_get_contents($url1);
    if ($response === false) {
        return null;
    }
    return $response;
}


function getDouaa() {
    $url2 = "https://yank7589.serv00.net/Visco/apido3ai.php?m=";
    $response = file_get_contents($url2);
    if ($response === false) {
        return null;
    }
    return $response;
}



function getPrayerTimes($city, $country) {
    if (!empty($city) && !empty($country)) {
        $url = "http://api.aladhan.com/v1/timingsByCity?city=" . urlencode($city) . "&country=" . urlencode($country) . "&method=";
        $response = file_get_contents($url);
        if ($response === false) {
            return null;
        }
        $data = json_decode($response, true);
        if (!isset($data["data"]["timings"])) {
            return null;
        }
        return $data["data"]["timings"];
    } else {
        return null;
    }
}



function translatePrayerTimes($prayerTimes) {
    if (is_array($prayerTimes)) {
        $translations = array(
            "Fajr" => "الفجر",
            "Sunrise" => "شروق الشمس",
            "Dhuhr" => "الظهر",
            "Asr" => "العصر",
            "Sunset" => "غروب الشمس",
            "Maghrib" => "المغرب",
            "Isha" => "العشاء",
            "Imsak" => "الإمساك",
            "Midnight" => "منتصف الليل",
            "Firstthird" => "الثلث الأول",
            "Lastthird" => "الثلث الأخير"
        );
        $translatedTimes = array();
        foreach ($prayerTimes as $key => $value) {
            if(isset($translations[$key])){
                $translatedTimes[$translations[$key]] = $value;
            }
        }
        return $translatedTimes;
    } else {
        return null;
    }
}


// Main logic
$update = json_decode(file_get_contents('php://input'));
$message = $update->message;
$id = $message->from->id;
$chat_id = $message->chat->id;
$from_id = $message->from->id;
$text = $message->text;
$name = $update->callback_query->from->first_name;
$user = $message->from->username;
if (isset($update->callback_query)) {
    $chat_id = $update->callback_query->message->chat->id;
    $message_id = $update->callback_query->message->message_id;
    $data  = $update->callback_query->data;
    $user = $update->callback_query->from->username;
}


$ex = explode("#", $data);



if ($text == "/start") {
    bot("sendmessage", [
        'chat_id'=>$chat_id,
        'text'=>"السلام عليكم ورحمة الله وبركاته
اهلا بك في بوت القرآن الكريم ،الرجاء منك اختيار احد الاقسام التالية:",
        'reply_to_message_id'=>$message->message_id,
        'parse_mode'=>"Markdown",
        'disable_web_page_preview'=>'true',
        'reply_markup'=>json_encode([ 
        'inline_keyboard'=>[
        [['text'=>'الاستـــــماع الى الـــقــرآن ' , 'callback_data'=>"quranmp3"]],
        [['text'=>'تـــصــــفــــح الــــقـــرآن بالصور' , 'callback_data'=>"quranpic"]],
        [['text'=>'تـــصــــفــــح الــــقـــرآن مكتوبا' , 'callback_data'=>"qurantext"]],
        [['text'=>'القـــرآن    بصـــيغة pdf' , 'callback_data'=>"quranpdf"]],
        [['text'=>'دعـــــــاء    و    ذكــــــــــر' , 'callback_data'=>"douaa&adhkar"]],
        [['text'=>'آيـــــــة    عشـــوائيـــــــــة' , 'callback_data'=>"random"]],
        [['text'=>'اوقـــــات      الـــــــصـــــلاة' , 'callback_data'=>"prayer"]],

]
])
]);
exit(); // Stop further execution
}


if ($data=="home" ){
    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"السلام عليكم ورحمة الله وبركاته
اهلا بك يا [$name](tg://user?id=$chat_id) في بوت القرآن الكريم ،الرجاء منك اختيار احد الاقسام التالية:",
        'reply_to_message_id'=>$message->message_id,
        'parse_mode'=>"MarkDown",
        'disable_web_page_preview'=>'true',
        'reply_markup'=>json_encode([ 
        'inline_keyboard'=>[
        [['text'=>'الاستـــــماع الى الـــقــرآن ' , 'callback_data'=>"quranmp3"]],
        [['text'=>'تـــصــــفــــح الــــقـــرآن بالصور' , 'callback_data'=>"quranpic"]],
        [['text'=>'تـــصــــفــــح الــــقـــرآن مكتوبا' , 'callback_data'=>"qurantext"]],
        [['text'=>'القـــرآن    بصـــيغة pdf' , 'callback_data'=>"quranpdf"]],
        [['text'=>'دعـــــــاء    و    ذكــــــــــر' , 'callback_data'=>"douaa&adhkar"]],
        [['text'=>'آيـــــــة    عشـــوائيـــــــــة' , 'callback_data'=>"random"]],
        [['text'=>'اوقـــــات      الـــــــصـــــلاة' , 'callback_data'=>"prayer"]],

]
])
]);
}


if ($data == "quranmp3" || $data == "page1") {
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => '
ــــــــــــــــــــــ𓇠ـــــــــــــــــــــ
  اختار   المقرئ   الذي   تريد
ــــــــــــــــــــــ𓇠ـــــــــــــــــــــ
',
        'parse_mode' => "markdown",
        'disable_web_page_preview' => 'true',
        "reply_markup" => json_encode([
            "inline_keyboard" => [
                [['text' => 'عبد الباسط عبد الصمد', 'callback_data' => "abdul_basit"]],
                [['text' => 'عبد الله المطرود', 'callback_data' => "al_matrood"]],
                [['text' => 'عبد الرحمن العوسي', 'callback_data' => "al_ausi"]],
                [['text' => 'أبو بكر الشاطري', 'callback_data' => "al_shatri"]],
                [['text' => 'أحمد العجمي', 'callback_data' => "al_ajmi"]],
                [['text' => 'فارس عباد', 'callback_data' => "abbad"]],
                [['text' => 'محمود خليل الحصري', 'callback_data' => "al_husori"]],
                [['text' => 'ماهر المعيقلي', 'callback_data' => "al_mueaqly"]],
                [['text' => 'محمد صديق المنشاوي', 'callback_data' => "sddeq"]],
                [['text' => 'رياض الجزائري', 'callback_data' => "رياض الجزائري"]],
                [['text' => 'التالي', 'callback_data' => "page2"]],
                [['text' => 'رجوع', 'callback_data' => "home"]],
            ]
        ])
    ]);
}

if ($data == "page2") {
    bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => '
ــــــــــــــــــــــ𓇠ـــــــــــــــــــــ
  اختار   المقرئ   الذي   تريد
ــــــــــــــــــــــ𓇠ـــــــــــــــــــــ
',
        'parse_mode' => "markdown",
        'disable_web_page_preview' => 'true',
        "reply_markup" => json_encode([
            "inline_keyboard" => [
                [['text' => 'سعد الغامدي', 'callback_data' => "سعد الغامدي"]],
                [['text' => 'سعد الغامدي(المصحف المعلم)', 'callback_data' => "سعد الغامدي(المصحف المعلم)"]],
                [['text' => 'محمود خليل الحصري(المصحف المعلم)', 'callback_data' => "محمود خليل الحصري(المصحف المعلم)"]],
                [['text' => 'محمود خليل الحصري(ورش عن نافع)', 'callback_data' => "محمود خليل الحصري(ورش عن نافع)"]],
                [['text' => 'سعود الشريم', 'callback_data' => "سعود الشريم"]],
                [['text' => 'سعود الشريم و عبد الرحمان السديس(المصحف المشترك)', 'callback_data' => "سعود الشريم و عبد الرحمان السديس(المصحف المشترك)"]],
                [['text' => 'محمد صديق المنشاوي (المصحف المعلم)', 'callback_data' => "محمد صديق المنشاوي (المصحف المعلم)"]],
                [['text' => 'محمد صديق المنشاوي (الصحف المجود)', 'callback_data' => "محمد صديق المنشاوي (الصحف المجود)"]],
                [['text' => 'سلمان العتيبي', 'callback_data' => "سلمان العتيبي"]],
                [['text' => 'صلاح بوخاطر', 'callback_data' => "صلاح بوخاطر"]],
                [['text' => 'عبدالرحمن السديس', 'callback_data' => "عبدالرحمن السديس"]],
                [['text' => 'فارس عباد(المصحف المعلم)', 'callback_data' => "فارس عباد(المصحف المعلم)"]],
                [['text' => 'ماهر المعيقلي', 'callback_data' => "ماهر المعيقلي"]],
                [['text' => 'محمود البنا', 'callback_data' => "محمود البنا"]],
                [['text' => 'مشاري العفاسي', 'callback_data' => "مشاري العفاسي"]],
                [['text' => 'ناصر القطامي', 'callback_data' => "ناصر القطامي"]],
                [['text' => 'هزاع البلوشي', 'callback_data' => "هزاع البلوشي"]],
                [['text' => 'ياسر الدوسري', 'callback_data' => "ياسر الدوسري"]],
                [['text' => 'ياسر القرشي', 'callback_data' => "ياسر القرشي"]],
                [['text' => 'ياسين الجزائري', 'callback_data' => "ياسين الجزائري"]],
                [['text' => 'السابق', 'callback_data' => "page1"]],
                [['text' => 'رجوع', 'callback_data' => "home"]],
            ]
        ])
    ]);
}



$reader = file_get_contents("data/$chat_id.txt");

if($data=="quranpic"){
    file_put_contents("data/$chat_id.txt","https://ia600400.us.archive.org/21/items/Quran4u_Quran_Brown");
    bot('EditMessageText',[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"اهلا بك يا [$name](tg://user?id=$chat_id) في قسم تصفح القرآن ، ارسل رقم الصفحة التي تريد",
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=>[
        [['text'=>'رجوع' , 'callback_data'=>"home"]],
        
]
])
]);
}


if($text=="604" or $text<"604"){
    bot('sendphoto',[
        'chat_id'=>$chat_id, 'photo'=>"$reader/000$text.jpg",
]);

    bot('sendphoto',[
        'chat_id'=>$chat_id, 'photo'=>"$reader/00$text.jpg",
]);

    bot('sendphoto',[
        'chat_id'=>$chat_id, 'photo'=>"$reader/0$text.jpg",
]);
}


if ($data == "random") {
        $ayahNumber = rand(1, 6236); 
        $ayahData = file_get_contents("http://api.alquran.cloud/v1/ayah/$ayahNumber/ar.abdulsamad");
if ($ayahData !== false) {
        $ayahData = json_decode($ayahData, true); 
        $ayahText = $ayahData['data']['text']; 
        $ayahPart = $ayahData['data']['surah']['number']; 
        $ayahHizb = $ayahData['data']['hizbQuarter']; 
        $ayahNumberInSurah = $ayahData['data']['numberInSurah']; 
        $ayahPage = $ayahData['data']['page'];
        $audioApiUrl = "http://api.alquran.cloud/v1/ayah/$ayahNumber/ar.abdulsamad"; 
        $audioResponse = file_get_contents($audioApiUrl);
        $audioData = json_decode($audioResponse, true);
        if ($audioData !== false && isset($audioData['data']['audio'])) {
        $audioUrl = $audioData['data']['audio']; 


        bot('EditMessageText', [
        'chat_id' => $chat_id,
        'message_id'=>$message_id,
        'text' => " *{$ayahData['data']['surah']['name']}* • \n\n*﴿ $ayahText ﴾* \n\n- الجزء: $ayahPart - ربع الحزب: $ayahHizb - الأية: $ayahNumberInSurah - الصفحة: $ayahPage . \n
[👇]($audioUrl)",
        'parse_mode' => 'markdown',
        'reply_markup' => json_encode([
        'inline_keyboard' => [
        [['text' => 'مرة اخرى ♻️', 'callback_data' => 'random']],
        [['text'=>"العودة 🔙",'callback_data'=>"home"]],

                    ]
                ])
            ]);
        }
    }
}


if ($data == "prayer") {
    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => "أهلاً بك يا [$name](tg://user?id=$chat_id) في قسم أوقات الصلاة يرجى إدخال اسم المدينة واسم البلد بالترتيب (مثال: العلمة, الجزائر) للحصول على أوقات الصلاة.",
        'parse_mode' => 'markdown',
    ]);
}

$parts = explode(",", $text);
if (count($parts) == 2) {
    $city = trim($parts[0]);
    $country = trim($parts[1]);

    $prayerTimes = getPrayerTimes($city, $country);
    if ($prayerTimes !== null) {
        $translatedTimes = translatePrayerTimes($prayerTimes);
        if ($translatedTimes !== null) {
            $response = "أوقات الصلاة في $city:\n";
            foreach ($translatedTimes as $prayer => $time) {
                $response .= "$prayer: $time\n";
            }
        } else {
            $response = "حدث خطأ أثناء ترجمة أوقات الصلاة.";
        }
    } else {
        $response = "لم يتم العثور على أوقات الصلاة للمدينة المحددة.";
    }

    bot('sendMessage', [
        'chat_id' => $chat_id,
        'text' => $response
    ]);
}



if ($data == "quranpdf") {
    bot('senddocument', [
        'chat_id' => $chat_id,
        'document' => "https://abdobnymrwan.alwaysdata.net/quran.pdf"

]);
}


$douaa = getDouaa();
$adhkar = getAdhkar();
if ($data == "douaa&adhkar") {
    bot('editMessagetext', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => " أهلاً بك  [$name](tg://user?id=$chat_id) في قسم الادعية والاذكار اختر ما تريد من القائمة ادناه: ",
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=>[
        [['text'=>'دعاء عشوائي' , 'callback_data'=>"douaa"],['text'=>'ذكر عشوائي' , 'callback_data'=>"adhkar"]],[['text'=>'اذكار الصباح و المساء', 'callback_data'=>"Z1"]],
        [['text'=>'رجوع 🔙' , 'callback_data'=>"home"]],
        
]
])
]);
}


if ($data == "douaa"){
    bot('editMessagetext', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $douaa,
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=>[
        [['text'=>'مرة اخرى ♻️' , 'callback_data'=>"douaa"]],
        [['text'=>'رجوع 🔙' , 'callback_data'=>"douaa&adhkar"]],
        [['text'=>"الصفحة الرئيسية 🏠",'callback_data'=>"home"]],
]
])
]);
}


if ($data == "adhkar"){
    bot('editMessagetext', [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => $adhkar,
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=>[
        [['text'=>'مرة اخرى ♻️' , 'callback_data'=>"adhkar"]],
        [['text'=>'رجوع 🔙' , 'callback_data'=>"douaa&adhkar"]],
        [['text'=>"الصفحة الرئيسية 🏠",'callback_data'=>"home"]],
        
]
])
]);
} 

if($data=="Z1"){
bot('editMessageText',[
 'chat_id'=>$chat_id,
 'message_id'=>$message_id,
 'text'=>"*
أذكار الصباح والمساء 👇
ــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ
بسم الله الرحمن الرحيم 
 اللَّهُ لَا إِلَهَ إِلَّا هُوَ الْحَيُّ الْقَيُّومُ لَا تَأْخُذُهُ سِنَةٌ وَلَا نَوْمٌ لَهُ مَا فِي السَّمَوَاتِ وَمَا فِي الْأَرْضِ مَنْ ذَا الَّذِي يَشْفَعُ عِنْدَهُ إِلَّا بِإِذْنِهِ يَعْلَمُ مَا بَيْنَ أَيْدِيهِمْ وَمَا خَلْفَهُمْ وَلَا يُحِيطُونَ بِشَيْءٍ مِنْ عِلْمِهِ إِلَّا بِمَا شَاءَ وَسِعَ كُرْسِيُّهُ السَّمَاوَاتِ وَالْأَرْضَ وَلَا يَئُودُهُ حِفْظُهُمَا وَهُوَ الْعَلِيُّ الْعَظِيمُ
ـــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ
بسم الله الرحمن الرحيم 
 قُلْ هُوَ اللَّهُ أَحَدٌ (1) اللَّهُ الصَّمَدُ (2) لَمْ يَلِدْ وَلَمْ يُولَدْ (3) وَلَمْ يَكُنْ لَهُ كُفُوًا أَحَدٌ (4)
3مرات
ــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ
بسم الله الرحمن الرحيم 
 قُلْ أَعُوذُ بِرَبِّ الْفَلَقِ (1) مِنْ شَرِّ مَا خَلَقَ (2) وَمِنْ شَرِّ غَاسِقٍ إِذَا وَقَبَ (3) وَمِنْ شَرِّ النَّفَّاثَاتِ فِي الْعُقَدِ (4) وَمِنْ شَرِّ حَاسِدٍ إِذَا حَسَدَ (5)
3مرات
ـــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ
بسم الله الرحمن الرحيم 
 قُلْ أَعُوذُ بِرَبِّ النَّاسِ (1) مَلِكِ النَّاسِ (2) إِلَهِ النَّاسِ (3) مِنْ شَرِّ الْوَسْوَاسِ الْخَنَّاسِ (4) الَّذِي يُوَسْوِسُ فِي صُدُورِ النَّاسِ (5) مِنَ الْجِنَّةِ وَالنَّاسِ (6)
3مرات
ــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ
اللّهـمَّ أَنْتَ رَبِّـي لا إلهَ إلاّ أَنْتَ ، خَلَقْتَنـي وَأَنا عَبْـدُك ، وَأَنا عَلـى عَهْـدِكَ وَوَعْـدِكَ ما اسْتَـطَعْـت ، أَعـوذُبِكَ مِنْ شَـرِّ ما صَنَـعْت ، أَبـوءُ لَـكَ بِنِعْـمَتِـكَ عَلَـيَّ وَأَبـوءُ بِذَنْـبي فَاغْفـِرْ لي فَإِنَّـهُ لا يَغْـفِرُ الذُّنـوبَ إِلاّ أَنْتَ .-------↯
من قالها من النهار موقنا بها. فمات من يومه قبل ان يمسي . فهو من اهل الجنة .وكذلك من قالها في المساء
ــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ
اللّهُـمَّ إِنِّـي أسْـأَلُـكَ العَـفْوَ وَالعـافِـيةَ في الدُّنْـيا وَالآخِـرَة ، اللّهُـمَّ إِنِّـي أسْـأَلُـكَ العَـفْوَ وَالعـافِـيةَ في ديني وَدُنْـيايَ وَأهْـلي وَمالـي ، اللّهُـمَّ اسْتُـرْ عـوْراتي وَآمِـنْ رَوْعاتـي ، اللّهُـمَّ احْفَظْـني مِن بَـينِ يَدَيَّ وَمِن خَلْفـي وَعَن يَمـيني وَعَن شِمـالي ، وَمِن فَوْقـي ، وَأَعـوذُ بِعَظَمَـتِكَ أَن أُغْـتالَ مِن تَحْتـي .-----↯
ويقال عند النوم ايضا.
ـــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ
أَصْـبَحْنا وَأَصْـبَحَ المُـلْكُ لله وَالحَمدُ لله ، لا إلهَ إلاّ اللّهُ وَحدَهُ لا شَريكَ لهُ، لهُ المُـلكُ ولهُ الحَمْـد، وهُوَ على كلّ شَيءٍ قدير ، رَبِّ أسْـأَلُـكَ خَـيرَ ما في هـذا اليوم وَخَـيرَ ما بَعْـدَه ، وَأَعـوذُ بِكَ مِنْ شَـرِّ ما في هـذا اليوم وَشَرِّ ما بَعْـدَه، رَبِّ أَعـوذُبِكَ مِنَ الْكَسَـلِ وَسـوءِ الْكِـبَر ، رَبِّ أَعـوذُبِكَ مِنْ عَـذابٍ في النّـارِ وَعَـذابٍ في القَـبْر.
ــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ
أَمْسَيْـنا وَأَمْسـى المـلكُ لله وَالحَمدُ لله ، لا إلهَ إلاّ اللّهُ وَحدَهُ لا شَريكَ لهُ، لهُ المُـلكُ ولهُ الحَمْـد، وهُوَ على كلّ شَيءٍ قدير ، رَبِّ أسْـأَلُـكَ خَـيرَ ما في هـذهِ اللَّـيْلَةِ وَخَـيرَ ما بَعْـدَهـا ، وَأَعـوذُ بِكَ مِنْ شَـرِّ ما في هـذهِ اللَّـيْلةِ وَشَرِّ ما بَعْـدَهـا ، رَبِّ أَعـوذُبِكَ مِنَ الْكَسَـلِ وَسـوءِ الْكِـبَر ، رَبِّ أَعـوذُ بِكَ مِنْ عَـذابٍ في النّـارِ وَعَـذابٍ في القَـبْر.
ــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ
بِسـمِ اللهِ الذي لا يَضُـرُّ مَعَ اسمِـهِ شَيءٌ في الأرْضِ وَلا في السّمـاءِ وَهـوَ السّمـيعُ العَلـيم ----↯
3مرات
ــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ
أَعـوذُبِكَلِمـاتِ اللّهِ التّـامّـاتِ مِنْ شَـرِّ ما خَلَـق .----↯
3مرات
ــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــــ*",
'disable_web_page_preview'=> true ,
 'parse_mode'=>"Markdown",
 'reply_markup'=>json_encode([
 'inline_keyboard'=>[
        [['text'=>'رجوع 🔙' , 'callback_data'=>"douaa&adhkar"]],
        [['text'=>"الصفحة الرئيسية 🏠",'callback_data'=>"home"]],
]
])
]);
}



$keyboard = [
    [['text' => "سورة الفاتحة", 'callback_data' => 'الفاتحة'], ['text' => "سورة البقرة", 'callback_data' => 'البقرة'], ['text' => "سورة آل عمران", 'callback_data' => 'آل عمران'], ['text' => "سورة النساء", 'callback_data' => 'النساء']],
    [['text' => "سورة المائدة", 'callback_data' => 'المائدة'], ['text' => "سورة الأنعام", 'callback_data' => 'الأنعام'], ['text' => "سورة الأعراف", 'callback_data' => 'الأعراف'], ['text' => "سورة الأنفال", 'callback_data' => 'الأنفال']],
    [['text' => "سورة التوبة", 'callback_data' => 'التوبة'], ['text' => "سورة يونس", 'callback_data' => 'يونس'], ['text' => "سورة هود", 'callback_data' => 'هود'], ['text' => "سورة يوسف", 'callback_data' => 'يوسف']],
    [['text' => "سورة الرعد", 'callback_data' => 'الرعد'], ['text' => "سورة ابراهيم", 'callback_data' => 'ابراهيم'], ['text' => "سورة الحجر", 'callback_data' => 'الحجر'], ['text' => "سورة النحل", 'callback_data' => 'النحل']],
    [['text' => "سورة الإسراء", 'callback_data' => 'الإسراء'], ['text' => "سورة الكهف", 'callback_data' => 'الكهف'], ['text' => "سورة مريم", 'callback_data' => 'مريم'], ['text' => "سورة طه", 'callback_data' => 'طه']],
    [['text' => "سورة الأنبياء", 'callback_data' => 'الأنبياء'], ['text' => "سورة الحج", 'callback_data' => 'الحج'], ['text' => "سورة المؤمنون", 'callback_data' => 'المؤمنون'], ['text' => "سورة النور", 'callback_data' => 'النور']],
    [['text' => "سورة الفرقان", 'callback_data' => 'الفرقان'], ['text' => "سورة الشعراء", 'callback_data' => 'الشعراء'], ['text' => "سورة النمل", 'callback_data' => 'النمل'], ['text' => "سورة القصص", 'callback_data' => 'القصص']],
    [['text' => "سورة العنكبوت", 'callback_data' => 'العنكبوت'], ['text' => "سورة الروم", 'callback_data' => 'الروم'], ['text' => "سورة لقمان", 'callback_data' => 'لقمان'], ['text' => "سورة السجدة", 'callback_data' => 'السجدة']],
    [['text' => "سورة الأحزاب", 'callback_data' => 'الأحزاب'], ['text' => "سورة سبأ", 'callback_data' => 'سبأ'], ['text' => "سورة فاطر", 'callback_data' => 'فاطر'], ['text' => "سورة يس", 'callback_data' => 'يس']],
    [['text' => "سورة الصافات", 'callback_data' => 'الصافات'], ['text' => "سورة ص", 'callback_data' => 'ص'], ['text' => "سورة الزمر", 'callback_data' => 'الزمر'], ['text' => "سورة غافر", 'callback_data' => 'غافر']],
    [['text' => "سورة فصلت", 'callback_data' => 'فصلت'], ['text' => "سورة الشورى", 'callback_data' => 'الشورى'], ['text' => "سورة الزخرف", 'callback_data' => 'الزخرف'], ['text' => "سورة الدخان", 'callback_data' => 'الدخان']],
    [['text' => "سورة الجاثية", 'callback_data' => 'الجاثية'], ['text' => "سورة الأحقاف", 'callback_data' => 'الأحقاف'], ['text' => "سورة محمد", 'callback_data' => 'محمد'], ['text' => "سورة الفتح", 'callback_data' => 'الفتح']],
    [['text' => "سورة الحجرات", 'callback_data' => 'الحجرات'], ['text' => "سورة ق", 'callback_data' => 'ق'], ['text' => "سورة الذاريات", 'callback_data' => 'الذاريات'], ['text' => "سورة الطور", 'callback_data' => 'الطور']],
    [['text' => "سورة النجم", 'callback_data' => 'النجم'], ['text' => "سورة القمر", 'callback_data' => 'القمر'], ['text' => "سورة الرحمن", 'callback_data' => 'الرحمن'], ['text' => "سورة الواقعة", 'callback_data' => 'الواقعة']],
    [['text'=>'التالي ⏩' , 'callback_data'=>"keyboard2"], ['text'=>'عودة 🔙' , 'callback_data'=>"quranmp3"]],
    [['text'=>"الصفحة الرئيسية 🏠",'callback_data'=>"home"]],
];

$keyboard2 = [
    [['text' => "سورة الحديد", 'callback_data' => 'الحديد'], ['text' => "سورة المجادلة", 'callback_data' => 'المجادلة'], ['text' => "سورة الحشر", 'callback_data' => 'الحشر'], ['text' => "سورة الممتحنة", 'callback_data' => 'الممتحنة']],
    [['text' => "سورة الصف", 'callback_data' => 'الصف'], ['text' => "سورة الجمعة", 'callback_data' => 'الجمعة'], ['text' => "سورة المنافقون", 'callback_data' => 'المنافقون'], ['text' => "سورة التغابن", 'callback_data' => 'التغابن']],
    [['text' => "سورة الطلاق", 'callback_data' => 'الطلاق'], ['text' => "سورة التحريم", 'callback_data' => 'التحريم'], ['text' => "سورة الملك", 'callback_data' => 'الملك'], ['text' => "سورة القلم", 'callback_data' => 'القلم']],
    [['text' => "سورة الحاقة", 'callback_data' => 'الحاقة'], ['text' => "سورة المعارج", 'callback_data' => 'المعارج'], ['text' => "سورة نوح", 'callback_data' => 'نوح'], ['text' => "سورة الجن", 'callback_data' => 'الجن']],
    [['text' => "سورة المزمل", 'callback_data' => 'المزمل'], ['text' => "سورة المدثر", 'callback_data' => 'المدثر'], ['text' => "سورة القيامة", 'callback_data' => 'القيامة'], ['text' => "سورة الانسان", 'callback_data' => 'الانسان']],
    [['text' => "سورة المرسلات", 'callback_data' => 'المرسلات'], ['text' => "سورة النبأ", 'callback_data' => 'النبأ'], ['text' => "سورة النازعات", 'callback_data' => 'النازعات'], ['text' => "سورة عبس", 'callback_data' => 'عبس']],
    [['text' => "سورة التكوير", 'callback_data' => 'التكوير'], ['text' => "سورة الانفطار", 'callback_data' => 'الانفطار'], ['text' => "سورة المطففين", 'callback_data' => 'المطففين'], ['text' => "سورة الانشقاق", 'callback_data' => 'الانشقاق']],
    [['text' => "سورة البروج", 'callback_data' => 'البروج'], ['text' => "سورة الطارق", 'callback_data' => 'الطارق'], ['text' => "سورة الأعلى", 'callback_data' => 'الأعلى'], ['text' => "سورة الغاشية", 'callback_data' => 'الغاشية']],
    [['text' => "سورة الفجر", 'callback_data' => 'الفجر'], ['text' => "سورة البلد", 'callback_data' => 'البلد'], ['text' => "سورة الشمس", 'callback_data' => 'الشمس'], ['text' => "سورة الليل", 'callback_data' => 'الليل']],
    [['text' => "سورة الضحى", 'callback_data' => 'الضحى'], ['text' => "سورة الشرح", 'callback_data' => 'الشرح'], ['text' => "سورة التين", 'callback_data' => 'التين'], ['text' => "سورة العلق", 'callback_data' => 'العلق']],
    [['text' => "سورة القدر", 'callback_data' => 'القدر'], ['text' => "سورة البينة", 'callback_data' => 'البينة'], ['text' => "سورة الزلزلة", 'callback_data' => 'الزلزلة'], ['text' => "سورة العاديات", 'callback_data' => 'العاديات']],
    [['text' => "سورة القارعة", 'callback_data' => 'القارعة'], ['text' => "سورة التكاثر", 'callback_data' => 'التكاثر'], ['text' => "سورة العصر", 'callback_data' => 'العصر'], ['text' => "سورة الهمزة", 'callback_data' => 'الهمزة']],
    [['text' => "سورة الفيل", 'callback_data' => 'الفيل'], ['text' => "سورة قريش", 'callback_data' => 'قريش'], ['text' => "سورة الماعون", 'callback_data' => 'الماعون'], ['text' => "سورة الكوثر", 'callback_data' => 'الكوثر']],
    [['text' => "سورة الكافرون", 'callback_data' => 'الكافرون'], ['text' => "سورة النصر", 'callback_data' => 'النصر'], ['text' => "سورة المسد", 'callback_data' => 'المسد'], ['text' => "سورة الإخلاص", 'callback_data' => 'الإخلاص']],
    [['text' => "سورة الفلق", 'callback_data' => 'الفلق'], ['text' => "سورة الناس", 'callback_data' => 'الناس']],
    [['text'=>'عودة 🔙' , 'callback_data'=>"quranmp3"]],
    [['text'=>"الصفحة الرئيسية 🏠",'callback_data'=>"home"]],
];

$keyboard3 = [
    [['text' => "سورة الفاتحة", 'callback_data' => '1'], ['text' => "سورة البقرة", 'callback_data' => '2'], ['text' => "سورة آل عمران", 'callback_data' => '3'], ['text' => "سورة النساء", 'callback_data' => '4']],
    [['text' => "سورة المائدة", 'callback_data' => '5'], ['text' => "سورة الأنعام", 'callback_data' => '6'], ['text' => "سورة الأعراف", 'callback_data' => '7'], ['text' => "سورة الأنفال", 'callback_data' => '8']],
    [['text' => "سورة التوبة", 'callback_data' => '9'], ['text' => "سورة يونس", 'callback_data' => '10'], ['text' => "سورة هود", 'callback_data' => '11'], ['text' => "سورة يوسف", 'callback_data' => '12']],
    [['text' => "سورة الرعد", 'callback_data' => '13'], ['text' => "سورة ابراهيم", 'callback_data' => '14'], ['text' => "سورة الحجر", 'callback_data' => '15'], ['text' => "سورة النحل", 'callback_data' => '16']],
    [['text' => "سورة الإسراء", 'callback_data' => '17'], ['text' => "سورة الكهف", 'callback_data' => '18'], ['text' => "سورة مريم", 'callback_data' => '19'], ['text' => "سورة طه", 'callback_data' => '20']],
    [['text' => "سورة الأنبياء", 'callback_data' => '21'], ['text' => "سورة الحج", 'callback_data' => '22'], ['text' => "سورة المؤمنون", 'callback_data' => '23'], ['text' => "سورة النور", 'callback_data' => '24']],
    [['text' => "سورة الفرقان", 'callback_data' => '25'], ['text' => "سورة الشعراء", 'callback_data' => '26'], ['text' => "سورة النمل", 'callback_data' => '27'], ['text' => "سورة القصص", 'callback_data' => '28']],
    [['text' => "سورة العنكبوت", 'callback_data' => '29'], ['text' => "سورة الروم", 'callback_data' => '30'], ['text' => "سورة لقمان", 'callback_data' => '31'], ['text' => "سورة السجدة", 'callback_data' => '32']],
    [['text' => "سورة الأحزاب", 'callback_data' => '33'], ['text' => "سورة سبأ", 'callback_data' => '34'], ['text' => "سورة فاطر", 'callback_data' => '35'], ['text' => "سورة يس", 'callback_data' => '36']],
    [['text' => "سورة الصافات", 'callback_data' => '37'], ['text' => "سورة ص", 'callback_data' => '38'], ['text' => "سورة الزمر", 'callback_data' => '39'], ['text' => "سورة غافر", 'callback_data' => '40']],
    [['text' => "سورة فصلت", 'callback_data' => '41'], ['text' => "سورة الشورى", 'callback_data' => '42'], ['text' => "سورة الزخرف", 'callback_data' => '43'], ['text' => "سورة الدخان", 'callback_data' => '44']],
    [['text' => "سورة الجاثية", 'callback_data' => '45'], ['text' => "سورة الأحقاف", 'callback_data' => '46'], ['text' => "سورة محمد", 'callback_data' => '47'], ['text' => "سورة الفتح", 'callback_data' => '48']],
    [['text' => "سورة الحجرات", 'callback_data' => '49'], ['text' => "سورة ق", 'callback_data' => '50'], ['text' => "سورة الذاريات", 'callback_data' => '51'], ['text' => "سورة الطور", 'callback_data' => '52']],
    [['text' => "سورة النجم", 'callback_data' => '53'], ['text' => "سورة القمر", 'callback_data' => '54'], ['text' => "سورة الرحمن", 'callback_data' => '55'], ['text' => "سورة الواقعة", 'callback_data' => '56']],
    [['text'=>'التالي ⏩' , 'callback_data'=>"keyboard4"], ['text'=>'عودة 🔙' , 'callback_data'=>"quranmp3"]],
    [['text'=>"الصفحة الرئيسية 🏠",'callback_data'=>"home"]],
];

$keyboard4 = [
    [['text' => "سورة الحديد", 'callback_data' => '57'], ['text' => "سورة المجادلة", 'callback_data' => '58'], ['text' => "سورة الحشر", 'callback_data' => '59'], ['text' => "سورة الممتحنة", 'callback_data' => '60']],
    [['text' => "سورة الصف", 'callback_data' => '61'], ['text' => "سورة الجمعة", 'callback_data' => '62'], ['text' => "سورة المنافقون", 'callback_data' => '63'], ['text' => "سورة التغابن", 'callback_data' => '64']],
    [['text' => "سورة الطلاق", 'callback_data' => '65'], ['text' => "سورة التحريم", 'callback_data' => '66'], ['text' => "سورة الملك", 'callback_data' => '67'], ['text' => "سورة القلم", 'callback_data' => '68']],
    [['text' => "سورة الحاقة", 'callback_data' => '69'], ['text' => "سورة المعارج", 'callback_data' => '70'], ['text' => "سورة نوح", 'callback_data' => '71'], ['text' => "سورة الجن", 'callback_data' => '72']],
    [['text' => "سورة المزمل", 'callback_data' => '73'], ['text' => "سورة المدثر", 'callback_data' => '74'], ['text' => "سورة القيامة", 'callback_data' => '75'], ['text' => "سورة الانسان", 'callback_data' => '76']],
    [['text' => "سورة المرسلات", 'callback_data' => '77'], ['text' => "سورة النبأ", 'callback_data' => '78'], ['text' => "سورة النازعات", 'callback_data' => '79'], ['text' => "سورة عبس", 'callback_data' => '80']],
    [['text' => "سورة التكوير", 'callback_data' => '81'], ['text' => "سورة الانفطار", 'callback_data' => '82'], ['text' => "سورة المطففين", 'callback_data' => '83'], ['text' => "سورة الانشقاق", 'callback_data' => '84']],
    [['text' => "سورة البروج", 'callback_data' => '85'], ['text' => "سورة الطارق", 'callback_data' => '86'], ['text' => "سورة الأعلى", 'callback_data' => '87'], ['text' => "سورة الغاشية", 'callback_data' => '88']],
    [['text' => "سورة الفجر", 'callback_data' => '89'], ['text' => "سورة البلد", 'callback_data' => '90'], ['text' => "سورة الشمس", 'callback_data' => '91'], ['text' => "سورة الليل", 'callback_data' => '92']],
    [['text' => "سورة الضحى", 'callback_data' => '93'], ['text' => "سورة الشرح", 'callback_data' => '94'], ['text' => "سورة التين", 'callback_data' => '95'], ['text' => "سورة العلق", 'callback_data' => '96']],
    [['text' => "سورة القدر", 'callback_data' => '97'], ['text' => "سورة البينة", 'callback_data' => '98'], ['text' => "سورة الزلزلة", 'callback_data' => '99'], ['text' => "سورة العاديات", 'callback_data' => '100']],
    [['text' => "سورة القارعة", 'callback_data' => '101'], ['text' => "سورة التكاثر", 'callback_data' => '102'], ['text' => "سورة العصر", 'callback_data' => '103'], ['text' => "سورة الهمزة", 'callback_data' => '104']],
    [['text' => "سورة الفيل", 'callback_data' => '105'], ['text' => "سورة قريش", 'callback_data' => '106'], ['text' => "سورة الماعون", 'callback_data' => '107'], ['text' => "سورة الكوثر", 'callback_data' => '108']],
    [['text' => "سورة الكافرون", 'callback_data' => '109'], ['text' => "سورة النصر", 'callback_data' => '110'], ['text' => "سورة المسد", 'callback_data' => '111'], ['text' => "سورة الإخلاص", 'callback_data' => '112']],
    [['text' => "سورة الفلق", 'callback_data' => '113'], ['text' => "سورة الناس", 'callback_data' => '114']],
    [['text'=>'عودة 🔙' , 'callback_data'=>"quranmp3"]],
    [['text'=>"الصفحة الرئيسية 🏠",'callback_data'=>"home"]],
];


if($data =="keyboard2"){

    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"اليك السور المتبقية
        اختر سورة من القائمة ادناه",
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=> $keyboard2 
        ])
    ]);
}


if($data =="keyboard4"){

    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"اليك السور المتبقية
        اختر سورة من القائمة ادناه",
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=> $keyboard4 
        ])
    ]);
}


if($data =="abdul_basit"){
    file_put_contents("data/$chat_id.txt",$data);
    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"أهلاً بك في قسم القرآن الكريم بصوت القاريء عبد الباسط عبد الصمد 

 اختر السورة التي تريد",
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=> $keyboard 
        ])
    ]);
}


if($data =="al_matrood"){
    file_put_contents("data/$chat_id.txt",$data);
    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"أهلاً بك في قسم القرآن الكريم بصوت القاريء عبد الله المطرود 

 اختر السورة التي تريد",
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=> $keyboard 
        ])
    ]);
}


if($data =="al_ausi"){
    file_put_contents("data/$chat_id.txt",$data);
    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"أهلاً بك في قسم القرآن الكريم بصوت القاريء عبد الرحمن العوسي 

 اختر السورة التي تريد ",
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=> $keyboard 
        ])
    ]);
}


if($data =="al_shatri"){
    file_put_contents("data/$chat_id.txt",$data);
    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"أهلاً بك في قسم القرآن الكريم بصوت القاريء أبو بكر الشاطري 

 اختر السورة التي تريد ",
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=> $keyboard 
        ])
    ]);
}


if($data =="al_ajmi"){
    file_put_contents("data/$chat_id.txt",$data);
    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"أهلاً بك في قسم القرآن الكريم بصوت القاريء أحمد العجمي 

 اختر السورة التي تريد",
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=> $keyboard 
        ])
    ]);
}


if($data =="abbad"){
    file_put_contents("data/$chat_id.txt",$data);
    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"أهلاً بك في قسم القرآن الكريم بصوت القاريء فارس عباد 

 اختر السورة التي تريد",
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=> $keyboard 
        ])
    ]);
}


if($data =="al_husori"){
    file_put_contents("data/$chat_id.txt",$data);
    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"أهلاً بك في قسم القرآن الكريم بصوت القاريء محمود خليل الحصري 

 اختر السورة التي تريد", 
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=> $keyboard 
        ])
    ]);
}


if($data =="al_mueaqly"){
    file_put_contents("data/$chat_id.txt",$data);
    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"أهلاً بك في قسم القرآن الكريم بصوت القاريء ماهر المعيقلي 

 اختر السورة التي تريد",    
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=> $keyboard 
        ])
    ]);
}


if($data =="sddeq"){
    file_put_contents("data/$chat_id.txt",$data);
    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"أهلاً بك في قسم القرآن الكريم بصوت القاريء محمد صديق المنشاوي 

 اختر السورة التي تريد",    
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
        "inline_keyboard"=> $keyboard
        ])
    ]);
}


$reader = file_get_contents("data/$chat_id.txt");

$suras = ["الفاتحة", "البقرة", "آل عمران", "النساء", "المائدة", "الأنعام", "الأعراف", "الأنفال", "التوبة", "يونس", "هود", "يوسف", "الرعد", "ابراهيم", "الحجر", "النحل", "الإسراء", "الكهف", "مريم", "طه", "الأنبياء", "الحج", "المؤمنون", "النور", "الفرقان", "الشعراء", "النمل", "القصص", "العنكبوت", "الروم", "لقمان", "السجدة", "الأحزاب", "سبإ", "فاطر", "يس", "الصافات", "ص", "الزمر", "غافر", "فصلت", "الشورى", "الزخرف", "الدخان", "الجاثية", "الأحقاف", "محمد", "الفتح", "الحجرات", "ق", "الذاريات", "الطور", "النجم", "القمر", "الرحمن", "الواقعة", "الحديد", "المجادلة", "الحشر", "الممتحنة", "الصف", "الجمعة", "المنافقون", "التغابن", "الطلاق", "التحريم", "الملك", "القلم", "الحاقة", "المعارج", "نوح", "الجن", "المزمل", "المدثر", "القيامة", "الانسان", "المرسلات", "النبإ", "النازعات", "عبس", "التكوير", "الإنفطار", "المطففين", "الإنشقاق", "البروج", "الطارق", "الأعلى", "الغاشية", "الفجر", "البلد", "الشمس", "الليل", "الضحى", "الشرح", "التين", "العلق", "القدر", "البينة", "الزلزلة", "العاديات", "القارعة", "التكاثر", "العصر", "الهمزة", "الفيل", "قريش", "الماعون", "الكوثر", "الكافرون", "النصر", "المسد", "الإخلاص", "الفلق", "الناس"];

if (in_array($data, $suras)) {
    $get = json_decode(file_get_contents("https://api-quran.com/quransql/mp3.php?text=".urlencode($data)."&reader=".urlencode($reader)));
    bot('sendaudio',[
        'chat_id' => $chat_id,
        'audio' => $get->url,
        'caption' => $get->caption,
        "reply_to_message_id" => $message_id,
    ]);
}



$reciters = [
    "رعد الكردي" => 33365,
    "رياض الجزائري" => 58184,
    "سعد الغامدي" => 788,
    "سعد الغامدي(المصحف المعلم)"=>39662,
    "محمود خليل الحصري(المصحف المعلم)"=>52026,
    "محمود خليل الحصري(ورش عن نافع)"=>14332,
    "سعود الشريم" => 2746,
    "سعود الشريم و عبد الرحمان السديس(المصحف المشترك)"=>4783,
    "محمد صديق المنشاوي (المصحف المعلم)"=>12201,
    "محمد صديق المنشاوي (الصحف المجود)"=>4537,
    "سلمان العتيبي" => 62580,
    "صلاح بوخاطر" => 2508,
    "عبدالرحمن السديس" => 9293,
    "فارس عباد" => 2148,
    "فارس عباد(المصحف المعلم)"=>40560,
    "ماهر المعيقلي" => 49336,
    "محمود البنا" => 21343,
    "مشاري العفاسي" => 1265,
    "ناصر القطامي" => 2024,
    "هزاع البلوشي" => 36450,
    "ياسر الدوسري" => 31199,
    "ياسر القرشي" => 62450,
    "ياسين الجزائري" => 23424,
];


if(array_key_exists($data, $reciters)){
    $sora_link = $reciters[$data];
    file_put_contents("data/$chat_id.txt", $sora_link);
    bot("EditMessageText",[
        'chat_id'=>$chat_id,
        'message_id'=>$message_id,
        'text'=>"أهلاً بك في قسم القرآن الكريم بصوت القاريء 
$data 

اختر السورة التي تريد",
        'parse_mode'=>"markdown",
        'disable_web_page_preview'=>true,
        "reply_markup"=>json_encode([
            "inline_keyboard"=> $keyboard3 
        ])
    ]);
}



$suras2 = range(1, 114);

if (in_array($data, $suras2)) {
    bot('sendaudio',[
        'chat_id' => $chat_id,
        'audio' => "http://t.me/quran1tv/".($data + $reader),
        "reply_to_message_id" => $message_id,
    ]);
}



$keyboard5 = [
    [['text' => "سورة الفاتحة", 'callback_data' => 'txt#1'], ['text' => "سورة البقرة", 'callback_data' => 'txt#2'], ['text' => "سورة آل عمران", 'callback_data' => 'txt#3'], ['text' => "سورة النساء", 'callback_data' => 'txt#4']],
    [['text' => "سورة المائدة", 'callback_data' => 'txt#5'], ['text' => "سورة الأنعام", 'callback_data' => 'txt#6'], ['text' => "سورة الأعراف", 'callback_data' => 'txt#7'], ['text' => "سورة الأنفال", 'callback_data' => 'txt#8']],
    [['text' => "سورة التوبة", 'callback_data' => 'txt#9'], ['text' => "سورة يونس", 'callback_data' => 'txt#10'], ['text' => "سورة هود", 'callback_data' => 'txt#11'], ['text' => "سورة يوسف", 'callback_data' => 'txt#12']],
    [['text' => "سورة الرعد", 'callback_data' => 'txt#13'], ['text' => "سورة ابراهيم", 'callback_data' => 'txt#14'], ['text' => "سورة الحجر", 'callback_data' => 'txt#15'], ['text' => "سورة النحل", 'callback_data' => 'txt#16']],
    [['text' => "سورة الإسراء", 'callback_data' => 'txt#17'], ['text' => "سورة الكهف", 'callback_data' => 'txt#18'], ['text' => "سورة مريم", 'callback_data' => 'txt#19'], ['text' => "سورة طه", 'callback_data' => 'txt#20']],
    [['text' => "سورة الأنبياء", 'callback_data' => 'txt#21'], ['text' => "سورة الحج", 'callback_data' => 'txt#22'], ['text' => "سورة المؤمنون", 'callback_data' => 'txt#23'], ['text' => "سورة النور", 'callback_data' => 'txt#24']],
    [['text' => "سورة الفرقان", 'callback_data' => 'txt#25'], ['text' => "سورة الشعراء", 'callback_data' => 'txt#26'], ['text' => "سورة النمل", 'callback_data' => 'txt#27'], ['text' => "سورة القصص", 'callback_data' => 'txt#28']],
    [['text' => "سورة العنكبوت", 'callback_data' => 'txt#29'], ['text' => "سورة الروم", 'callback_data' => 'txt#30'], ['text' => "سورة لقمان", 'callback_data' => 'txt#31'], ['text' => "سورة السجدة", 'callback_data' => 'txt#32']],
    [['text' => "سورة الأحزاب", 'callback_data' => 'txt#33'], ['text' => "سورة سبأ", 'callback_data' => 'txt#34'], ['text' => "سورة فاطر", 'callback_data' => 'txt#35'], ['text' => "سورة يس", 'callback_data' => 'txt#36']],
    [['text' => "سورة الصافات", 'callback_data' => 'txt#37'], ['text' => "سورة ص", 'callback_data' => 'txt#38'], ['text' => "سورة الزمر", 'callback_data' => 'txt#39'], ['text' => "سورة غافر", 'callback_data' => 'txt#40']],
    [['text' => "سورة فصلت", 'callback_data' => 'txt#41'], ['text' => "سورة الشورى", 'callback_data' => 'txt#42'], ['text' => "سورة الزخرف", 'callback_data' => 'txt#43'], ['text' => "سورة الدخان", 'callback_data' => 'txt#44']],
    [['text' => "سورة الجاثية", 'callback_data' => 'txt#45'], ['text' => "سورة الأحقاف", 'callback_data' => 'txt#46'], ['text' => "سورة محمد", 'callback_data' => 'txt#47'], ['text' => "سورة الفتح", 'callback_data' => 'txt#48']],
    [['text' => "سورة الحجرات", 'callback_data' => 'txt#49'], ['text' => "سورة ق", 'callback_data' => 'txt#50'], ['text' => "سورة الذاريات", 'callback_data' => 'txt#51'], ['text' => "سورة الطور", 'callback_data' => 'txt#52']],
    [['text' => "سورة النجم", 'callback_data' => 'txt#53'], ['text' => "سورة القمر", 'callback_data' => 'txt#54'], ['text' => "سورة الرحمن", 'callback_data' => 'txt#55'], ['text' => "سورة الواقعة", 'callback_data' => 'txt#56']],
    [['text'=>'التالي ⏩' , 'callback_data'=>"keyboard6"], ['text'=>'عودة 🔙' , 'callback_data'=>"quranmp3"]],
    [['text'=>"الصفحة الرئيسية 🏠",'callback_data'=>"home"]],
];


$keyboard6 = [
    [['text' => "سورة الحديد", 'callback_data' => 'txt#57'], ['text' => "سورة المجادلة", 'callback_data' => 'txt#58'], ['text' => "سورة الحشر", 'callback_data' => 'txt#59'], ['text' => "سورة الممتحنة", 'callback_data' => 'txt#60']],
    [['text' => "سورة الصف", 'callback_data' => 'txt#61'], ['text' => "سورة الجمعة", 'callback_data' => 'txt#62'], ['text' => "سورة المنافقون", 'callback_data' => 'txt#63'], ['text' => "سورة التغابن", 'callback_data' => 'txt#64']],
    [['text' => "سورة الطلاق", 'callback_data' => 'txt#65'], ['text' => "سورة التحريم", 'callback_data' => 'txt#66'], ['text' => "سورة الملك", 'callback_data' => 'txt#67'], ['text' => "سورة القلم", 'callback_data' => 'txt#68']],
    [['text' => "سورة الحاقة", 'callback_data' => 'txt#69'], ['text' => "سورة المعارج", 'callback_data' => 'txt#70'], ['text' => "سورة نوح", 'callback_data' => 'txt#71'], ['text' => "سورة الجن", 'callback_data' => 'txt#72']],
    [['text' => "سورة المزمل", 'callback_data' => 'txt#73'], ['text' => "سورة المدثر", 'callback_data' => 'txt#74'], ['text' => "سورة القيامة", 'callback_data' => 'txt#75'], ['text' => "سورة الانسان", 'callback_data' => 'txt#76']],
    [['text' => "سورة المرسلات", 'callback_data' => 'txt#77'], ['text' => "سورة النبأ", 'callback_data' => 'txt#78'], ['text' => "سورة النازعات", 'callback_data' => 'txt#79'], ['text' => "سورة عبس", 'callback_data' => 'txt#80']],
    [['text' => "سورة التكوير", 'callback_data' => 'txt#81'], ['text' => "سورة الانفطار", 'callback_data' => 'txt#82'], ['text' => "سورة المطففين", 'callback_data' => 'txt#83'], ['text' => "سورة الانشقاق", 'callback_data' => 'txt#84']],
    [['text' => "سورة البروج", 'callback_data' => 'txt#85'], ['text' => "سورة الطارق", 'callback_data' => 'txt#86'], ['text' => "سورة الأعلى", 'callback_data' => 'txt#87'], ['text' => "سورة الغاشية", 'callback_data' => 'txt#88']],
    [['text' => "سورة الفجر", 'callback_data' => 'txt#89'], ['text' => "سورة البلد", 'callback_data' => 'txt#90'], ['text' => "سورة الشمس", 'callback_data' => 'txt#91'], ['text' => "سورة الليل", 'callback_data' => 'txt#92']],
    [['text' => "سورة الضحى", 'callback_data' => 'txt#93'], ['text' => "سورة الشرح", 'callback_data' => 'txt#94'], ['text' => "سورة التين", 'callback_data' => 'txt#95'], ['text' => "سورة العلق", 'callback_data' => 'txt#96']],
    [['text' => "سورة القدر", 'callback_data' => 'txt#97'], ['text' => "سورة البينة", 'callback_data' => 'txt#98'], ['text' => "سورة الزلزلة", 'callback_data' => 'txt#99'], ['text' => "سورة العاديات", 'callback_data' => 'txt#100']],
    [['text' => "سورة القارعة", 'callback_data' => 'txt#101'], ['text' => "سورة التكاثر", 'callback_data' => 'txt#102'], ['text' => "سورة العصر", 'callback_data' => 'txt#103'], ['text' => "سورة الهمزة", 'callback_data' => 'txt#104']],
    [['text' => "سورة الفيل", 'callback_data' => 'txt#105'], ['text' => "سورة قريش", 'callback_data' => 'txt#106'], ['text' => "سورة الماعون", 'callback_data' => 'txt#107'], ['text' => "سورة الكوثر", 'callback_data' => 'txt#108']],
    [['text' => "سورة الكافرون", 'callback_data' => 'txt#109'], ['text' => "سورة النصر", 'callback_data' => 'txt#110'], ['text' => "سورة المسد", 'callback_data' => 'txt#111'], ['text' => "سورة الإخلاص", 'callback_data' => 'txt#112']],
    [['text' => "سورة الفلق", 'callback_data' => 'txt#113'], ['text' => "سورة الناس", 'callback_data' => 'txt#114']],
    [['text'=>'عودة 🔙' , 'callback_data'=>"quranmp3"]],
    [['text'=>"الصفحة الرئيسية 🏠",'callback_data'=>"home"]],
];



if ($data == "keyboard6") {
    bot("EditMessageText", [
        'chat_id' => $chat_id,
        'message_id' => $message_id,
        'text' => "اليك السور المتبقية\nاختر سورة من القائمة ادناه",
        'parse_mode' => "markdown",
        'disable_web_page_preview' => true,
        "reply_markup" => json_encode([
            "inline_keyboard" => $keyboard6
        ])
    ]);
}




if ($data == "qurantext") {
      bot('EditMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => "أهلاً بك في قسم القرآن الكريم.مكتوب اختر السورة التي تريدها ",
        'parse_mode' => "markdown",
        'disable_web_page_preview' => true,
        "reply_markup" => json_encode([
            "inline_keyboard" => $keyboard5
        ])
    ]);
}




$ex = explode("#", $data);

$jsonData = json_decode(file_get_contents("https://abdobnymrwan.alwaysdata.net/quran.json"), true);

foreach ($jsonData['data']['surahs'] as $surah) {
    if ($surah['number'] == intval($ex[1])) {
        $type = $surah["revelationType"];
        if ($type == "medinan") {
            $type = "المــدينــة";
        } else {
            $type = "مــكة";
        }

        $totalAyahs = count($surah['ayahs']);
        $messageText = "*" . $surah['name'] . "*\n" . "*مكان نزولها : " . $type . "*\n". "* عدد آياتها : " . $totalAyahs . "*\n\n*_اعوذ بالله من الشيطان الرجيم_*\n";
        $textChunk = "";
        $reply_markup = ['inline_keyboard' => []];

        for ($i = 0; $i < $totalAyahs; $i++) {
            $textChunk .= $surah['ayahs'][$i]['text'] . " ﴿" . $surah['ayahs'][$i]['numberInSurah'] . "﴾ ";
            $count = mb_strlen($textChunk, "utf-8");
            if ($count > 3000) {
                break;
            }
        }

        if ($i < $totalAyahs) {
            $reply_markup['inline_keyboard'][] = [['text' => 'التالي ', 'callback_data' => "qra_next#".$surah['number']."#$i"]];
        }
        $reply_markup['inline_keyboard'][] = [['text' => 'اغلاق ❎', 'callback_data' => "qurantext"]];
        $reply_markup = json_encode($reply_markup);

        bot('EditMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $messageText . $textChunk,
            'parse_mode' => "markdown",
            'disable_web_page_preview' => true,
            'reply_markup' => $reply_markup
        ]);
    }
}

if ($ex[0] == "qra_next") {
    $s = $ex[1];
    $p = intval($ex[2]) + 1;
    $jsonData = json_decode(file_get_contents("https://abdobnymrwan.alwaysdata.net/quran.json"), true);
    foreach ($jsonData['data']['surahs'] as $surah) {
        if ($surah['number'] == intval($s)) {
            $name = $surah['name'];
            $totalAyahs = count($surah['ayahs']);
            $textChunk = "";  // Reset the text chunk
            $reply_markup = ['inline_keyboard' => []];

            for ($i = $p; $i < $totalAyahs; $i++) {
                $textChunk .= $surah['ayahs'][$i]['text'] . " ﴿" . $surah['ayahs'][$i]['numberInSurah'] . "﴾ ";
                $count = mb_strlen($textChunk, "utf-8");
                if ($count > 3000) {
                    break;
                }
            }

            if ($i < $totalAyahs) {
                $reply_markup['inline_keyboard'][] = [['text' => 'التالي ', 'callback_data' => "qra_next#".$surah['number']."#$i"]];
            }
            $reply_markup['inline_keyboard'][] = [['text' => 'اغلاق ❎', 'callback_data' => "qurantext"]];
            $reply_markup = json_encode($reply_markup);

            bot('EditMessageText', [
                'chat_id' => $chat_id,
                'message_id' => $message_id,
                'text' => $textChunk,
                'parse_mode' => "markdown",
                'reply_markup' => $reply_markup
            ]);
        }
    }
}



?>
