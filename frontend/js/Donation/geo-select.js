export default function(donationLexicon) {
	
  // populate country and regions
var geo_data = {
	countryData: {
		"chi": {
			"HK": "香港",
			"TW": "臺灣",
			"CN": "中國大陸",
			"MO": "澳門",
			"JP": "日本",
			"KR": "韓國",
			"US": "美國",
			"CA": "加拿大",
			"AU": "澳洲",
			"NZ": "紐西蘭",
			"GB": "英國",
			"NL": "荷蘭",
			"FR": "法國",
			"SG": "新加坡",
			"MY": "馬來西亞",
			"ID": "印尼",
			"PH": "菲律賓",
			"BN": "汶萊",
			"TH": "泰國",
			"VN": "越南",
			"DE": "德國",
			"ZA": "南非",
			"OT": "其他"
		},
		"eng": {
			"HK": "Hong Kong",
			"TW": "Taiwan",
			"CN": "China",
			"MO": "Macau",
			"JP": "Japan",
			"KR": "South Korea",
			"US": "United States",
			"CA": "Canada",
			"AU": "Australia",
			"NZ": "New Zealand",
			"GB": "Great Britain",
			"NL": "Netherlands",
			"FR": "France",
			"SG": "Singapore",
			"MY": "Malaysia",
			"ID": "Indonesia",
			"PH": "Philippines",
			"BN": "Brunei",
			"TH": "Thailand",
			"VN": "Vietnam",
			"DE": "Germany",
			"ZA": "South Africa",
			"OT": "Others"
		}
	},
	twData: {
		"chi": {
			"基隆市": {
				"仁愛區": "200",
				"信義區": "201",
				"中正區": "202",
				"中山區": "203",
				"安樂區": "204",
				"暖暖區": "205",
				"七堵區": "206"
			},
			"臺北市": {
				"中正區": "100",
				"大同區": "103",
				"中山區": "104",
				"松山區": "105",
				"大安區": "106",
				"萬華區": "108",
				"信義區": "110",
				"士林區": "111",
				"北投區": "112",
				"內湖區": "114",
				"南港區": "115",
				"文山區": "116"
			},
			"新北市": {
				"萬里區": "207",
				"金山區": "208",
				"板橋區": "220",
				"汐止區": "221",
				"深坑區": "222",
				"石碇區": "223",
				"瑞芳區": "224",
				"平溪區": "226",
				"雙溪區": "227",
				"貢寮區": "228",
				"新店區": "231",
				"坪林區": "232",
				"烏來區": "233",
				"永和區": "234",
				"中和區": "235",
				"土城區": "236",
				"三峽區": "237",
				"樹林區": "238",
				"鶯歌區": "239",
				"三重區": "241",
				"新莊區": "242",
				"泰山區": "243",
				"林口區": "244",
				"蘆洲區": "247",
				"五股區": "248",
				"八里區": "249",
				"淡水區": "251",
				"三芝區": "252",
				"石門區": "253"
			},
			"宜蘭縣": {
				"宜蘭市": "260",
				"頭城鎮": "261",
				"礁溪鄉": "262",
				"壯圍鄉": "263",
				"員山鄉": "264",
				"羅東鎮": "265",
				"三星鄉": "266",
				"大同鄉": "267",
				"五結鄉": "268",
				"冬山鄉": "269",
				"蘇澳鎮": "270",
				"南澳鄉": "272",
				"釣魚台列嶼": "290"
			},
			"新竹市": {
				"東區": "300",
				"北區": "300",
				"香山區": "300"
			},
			"新竹縣": {
				"竹北市": "302",
				"湖口鄉": "303",
				"新豐鄉": "304",
				"新埔鎮": "305",
				"關西鎮": "306",
				"芎林鄉": "307",
				"寶山鄉": "308",
				"竹東鎮": "310",
				"五峰鄉": "311",
				"橫山鄉": "312",
				"尖石鄉": "313",
				"北埔鄉": "314",
				"峨嵋鄉": "315"
			},
			"桃園市": {
				"中壢區": "320",
				"平鎮區": "324",
				"龍潭區": "325",
				"楊梅區": "326",
				"新屋區": "327",
				"觀音區": "328",
				"桃園區": "330",
				"龜山區": "333",
				"八德區": "334",
				"大溪區": "335",
				"復興區": "336",
				"大園區": "337",
				"蘆竹區": "338"
			},
			"苗栗縣": {
				"竹南鎮": "350",
				"頭份鎮": "351",
				"三灣鄉": "352",
				"南庄鄉": "353",
				"獅潭鄉": "354",
				"後龍鎮": "356",
				"通霄鎮": "357",
				"苑裡鎮": "358",
				"苗栗市": "360",
				"造橋鄉": "361",
				"頭屋鄉": "362",
				"公館鄉": "363",
				"大湖鄉": "364",
				"泰安鄉": "365",
				"銅鑼鄉": "366",
				"三義鄉": "367",
				"西湖鄉": "368",
				"卓蘭鎮": "369"
			},
			"臺中市": {
				"中區": "400",
				"東區": "401",
				"南區": "402",
				"西區": "403",
				"北區": "404",
				"北屯區": "406",
				"西屯區": "407",
				"南屯區": "408",
				"太平區": "411",
				"大里區": "412",
				"霧峰區": "413",
				"烏日區": "414",
				"豐原區": "420",
				"后里區": "421",
				"石岡區": "422",
				"東勢區": "423",
				"和平區": "424",
				"新社區": "426",
				"潭子區": "427",
				"大雅區": "428",
				"神岡區": "429",
				"大肚區": "432",
				"沙鹿區": "433",
				"龍井區": "434",
				"梧棲區": "435",
				"清水區": "436",
				"大甲區": "437",
				"外埔區": "438",
				"大安區": "439"
			},
			"彰化縣": {
				"彰化市": "500",
				"芬園鄉": "502",
				"花壇鄉": "503",
				"秀水鄉": "504",
				"鹿港鎮": "505",
				"福興鄉": "506",
				"線西鄉": "507",
				"和美鎮": "508",
				"伸港鄉": "509",
				"員林鎮": "510",
				"社頭鄉": "511",
				"永靖鄉": "512",
				"埔心鄉": "513",
				"溪湖鎮": "514",
				"大村鄉": "515",
				"埔鹽鄉": "516",
				"田中鎮": "520",
				"北斗鎮": "521",
				"田尾鄉": "522",
				"埤頭鄉": "523",
				"溪州鄉": "524",
				"竹塘鄉": "525",
				"二林鎮": "526",
				"大城鄉": "527",
				"芳苑鄉": "528",
				"二水鄉": "530"
			},
			"南投縣": {
				"南投市": "540",
				"中寮鄉": "541",
				"草屯鎮": "542",
				"國姓鄉": "544",
				"埔里鎮": "545",
				"仁愛鄉": "546",
				"名間鄉": "551",
				"集集鎮": "552",
				"水里鄉": "553",
				"魚池鄉": "555",
				"信義鄉": "556",
				"竹山鎮": "557",
				"鹿谷鄉": "558"
			},
			"嘉義市": {
				"東區": "600",
				"西區": "600"
			},
			"嘉義縣": {
				"番路鄉": "602",
				"梅山鄉": "603",
				"竹崎鄉": "604",
				"阿里山": "605",
				"中埔鄉": "606",
				"大埔鄉": "607",
				"水上鄉": "608",
				"鹿草鄉": "611",
				"太保市": "612",
				"朴子市": "613",
				"東石鄉": "614",
				"六腳鄉": "615",
				"新港鄉": "616",
				"民雄鄉": "621",
				"大林鎮": "622",
				"溪口鄉": "623",
				"義竹鄉": "624",
				"布袋鎮": "625"
			},
			"雲林縣": {
				"斗南鎮": "630",
				"大埤鄉": "631",
				"虎尾鎮": "632",
				"土庫鎮": "633",
				"褒忠鄉": "634",
				"東勢鄉": "635",
				"臺西鄉": "636",
				"崙背鄉": "637",
				"麥寮鄉": "638",
				"斗六市": "640",
				"林內鄉": "643",
				"古坑鄉": "646",
				"莿桐鄉": "647",
				"西螺鎮": "648",
				"二崙鄉": "649",
				"北港鎮": "651",
				"水林鄉": "652",
				"口湖鄉": "653",
				"四湖鄉": "654",
				"元長鄉": "655"
			},
			"臺南市": {
				"中西區": "700",
				"東區": "701",
				"南區": "702",
				"北區": "704",
				"安平區": "708",
				"安南區": "709",
				"永康區": "710",
				"歸仁區": "711",
				"新化區": "712",
				"左鎮區": "713",
				"玉井區": "714",
				"楠西區": "715",
				"南化區": "716",
				"仁德區": "717",
				"關廟區": "718",
				"龍崎區": "719",
				"官田區": "720",
				"麻豆區": "721",
				"佳里區": "722",
				"西港區": "723",
				"七股區": "724",
				"將軍區": "725",
				"學甲區": "726",
				"北門區": "727",
				"新營區": "730",
				"後壁區": "731",
				"白河區": "732",
				"東山區": "733",
				"六甲區": "734",
				"下營區": "735",
				"柳營區": "736",
				"鹽水區": "737",
				"善化區": "741",
				"大內區": "742",
				"山上區": "743",
				"新市區": "744",
				"安定區": "745"
			},
			"高雄市": {
				"新興區": "800",
				"前金區": "801",
				"苓雅區": "802",
				"鹽埕區": "803",
				"鼓山區": "804",
				"旗津區": "805",
				"前鎮區": "806",
				"三民區": "807",
				"楠梓區": "811",
				"小港區": "812",
				"左營區": "813",
				"仁武區": "814",
				"大社區": "815",
				"岡山區": "820",
				"路竹區": "821",
				"阿蓮區": "822",
				"田寮鄉": "823",
				"燕巢區": "824",
				"橋頭區": "825",
				"梓官區": "826",
				"彌陀區": "827",
				"永安區": "828",
				"湖內鄉": "829",
				"鳳山區": "830",
				"大寮區": "831",
				"林園區": "832",
				"鳥松區": "833",
				"大樹區": "840",
				"旗山區": "842",
				"美濃區": "843",
				"六龜區": "844",
				"內門區": "845",
				"杉林區": "846",
				"甲仙區": "847",
				"桃源區": "848",
				"那瑪夏區": "849",
				"茂林區": "851",
				"茄萣區": "852"
			},
			"屏東縣": {
				"屏東市": "900",
				"三地門": "901",
				"霧臺鄉": "902",
				"瑪家鄉": "903",
				"九如鄉": "904",
				"里港鄉": "905",
				"高樹鄉": "906",
				"鹽埔鄉": "907",
				"長治鄉": "908",
				"麟洛鄉": "909",
				"竹田鄉": "911",
				"內埔鄉": "912",
				"萬丹鄉": "913",
				"潮州鎮": "920",
				"泰武鄉": "921",
				"來義鄉": "922",
				"萬巒鄉": "923",
				"崁頂鄉": "924",
				"新埤鄉": "925",
				"南州鄉": "926",
				"林邊鄉": "927",
				"東港鎮": "928",
				"琉球鄉": "929",
				"佳冬鄉": "931",
				"新園鄉": "932",
				"枋寮鄉": "940",
				"枋山鄉": "941",
				"春日鄉": "942",
				"獅子鄉": "943",
				"車城鄉": "944",
				"牡丹鄉": "945",
				"恆春鎮": "946",
				"滿州鄉": "947"
			},
			"臺東縣": {
				"臺東市": "950",
				"綠島鄉": "951",
				"蘭嶼鄉": "952",
				"延平鄉": "953",
				"卑南鄉": "954",
				"鹿野鄉": "955",
				"關山鎮": "956",
				"海端鄉": "957",
				"池上鄉": "958",
				"東河鄉": "959",
				"成功鎮": "961",
				"長濱鄉": "962",
				"太麻里鄉": "963",
				"金峰鄉": "964",
				"大武鄉": "965",
				"達仁鄉": "966"
			},
			"花蓮縣": {
				"花蓮市": "970",
				"新城鄉": "971",
				"秀林鄉": "972",
				"吉安鄉": "973",
				"壽豐鄉": "974",
				"鳳林鎮": "975",
				"光復鄉": "976",
				"豐濱鄉": "977",
				"瑞穗鄉": "978",
				"萬榮鄉": "979",
				"玉里鎮": "981",
				"卓溪鄉": "982",
				"富里鄉": "983"
			},
			"金門縣": {
				"金沙鎮": "890",
				"金湖鎮": "891",
				"金寧鄉": "892",
				"金城鎮": "893",
				"烈嶼鄉": "894",
				"烏坵鄉": "896"
			},
			"連江縣": {
				"南竿鄉": "209",
				"北竿鄉": "210",
				"莒光鄉": "211",
				"東引鄉": "212"
			},
			"澎湖縣": {
				"馬公市": "880",
				"西嶼鄉": "881",
				"望安鄉": "882",
				"七美鄉": "883",
				"白沙鄉": "884",
				"湖西鄉": "885"
			},
			"南海諸島": {
				"東沙": "817",
				"南沙": "819"
			}
		},
		"eng": {
			"Keelung City": {
				"Ren'ai District": "200",
				"Xinyi District": "201",
				"Zhongzheng District": "202",
				"Zhongshan District": "203",
				"Anle District": "204",
				"Nuannuan District": "205",
				"Qidu District": "206"
			},
			"Taipei City": {
				"Zhongzheng District": "100",
				"Datong District": "103",
				"Zhongshan District": "104",
				"Songshan District": "105",
				"Da'an District": "106",
				"Wanhua District": "108",
				"Xinyi District": "110",
				"Shilin District": "111",
				"Beitou District": "112",
				"Neihu District": "114",
				"Nangang District": "115",
				"Wenshan District": "116"
			},
			"New Taipei City": {
				"Wanli District": "207",
				"Jinshan District": "208",
				"Banqiao District": "220",
				"Xizhi District": "221",
				"Shenkeng District": "222",
				"Shiding District": "223",
				"Ruifang District": "224",
				"Pingxi District": "226",
				"Shuangxi District": "227",
				"Gongliao District": "228",
				"Xindian District": "231",
				"Pinglin District": "232",
				"Wulai District": "233",
				"Yonghe District": "234",
				"Zhonghe District": "235",
				"Tucheng District": "236",
				"Sanxia District": "237",
				"Shulin District": "238",
				"Yingge District": "239",
				"Sanchong District": "241",
				"Xinzhuang District": "242",
				"Taishan District": "243",
				"Linkou District": "244",
				"Luzhou District": "247",
				"Wugu District": "248",
				"Bali District": "249",
				"Tamsui District": "251",
				"Sanzhi District": "252",
				"Shimen District": "253"
			},
			"Yilan County": {
				"Yilan City": "260",
				"Toucheng Township": "261",
				"Jiaoxi Township": "262",
				"Zhuangwei Township": "263",
				"Yuanshan Township": "264",
				"Luodong Township": "265",
				"Sanxing Township": "266",
				"Datong Township": "267",
				"Wujie Township": "268",
				"Dongshan Township": "269",
				"Su'ao Township": "270",
				"Nan'ao Township": "272"
			},
			"Hsinchu City": {
				"East District": "300",
				"North District": "300",
				"Siangshan District": "300"
			},
			"Hsinchu County": {
				"Zhubei City": "302",
				"Hukou Township": "303",
				"Xinfeng Township": "304",
				"Xinpu Township": "305",
				"Guanxi Township": "306",
				"Qionglin Township": "307",
				"Baoshan Township": "308",
				"Zhudong Township": "310",
				"Wufeng Township": "311",
				"Hengshan Township": "312",
				"Jianshih Township": "313",
				"Beipu Township": "314",
				"Emei Township": "315"
			},
			"Taoyuan City": {
				"Zhongli District": "320",
				"Pingzhen District": "324",
				"Longtan District": "325",
				"Yangmei District": "326",
				"Xinwu District": "327",
				"Guanyin District": "328",
				"Taoyuan District": "330",
				"Guishan District": "333",
				"Bade District": "334",
				"Daxi District": "335",
				"Fuxing District": "336",
				"Dayuan District": "337",
				"Luzhu District": "338"
			},
			"Miaoli County": {
				"Zhunan Township": "350",
				"Toufen City": "351",
				"Sanwan Township": "352",
				"Nanzhuang Township": "353",
				"Shitan Township": "354",
				"Houlong Township": "356",
				"Tongxiao Township": "357",
				"Yuanli Township": "358",
				"Miaoli City": "360",
				"Zaoqiao Township": "361",
				"Touwu Township": "362",
				"Gongguan Township": "363",
				"Dahu Township": "364",
				"Tai'an Township": "365",
				"Tongluo Township": "366",
				"Sanyi Township": "367",
				"Xihu Township": "368",
				"Zhuolan Township": "369"
			},
			"Taichung City": {
				"Central District": "400",
				"East District": "401",
				"South District": "402",
				"West District": "403",
				"North District": "404",
				"Beitun District": "406",
				"Xitun District": "407",
				"Nantun District": "408",
				"Taiping District": "411",
				"Dali District": "412",
				"Wufeng District": "413",
				"Wuri District": "414",
				"Fengyuan District": "420",
				"Houli District": "421",
				"Shigang District": "422",
				"Dongshi District": "423",
				"Heping District": "424",
				"Xinshe District": "426",
				"Tanzi District": "427",
				"Daya District": "428",
				"Shengang District": "429",
				"Dadu District": "432",
				"Shalu District": "433",
				"Longjing District": "434",
				"Wuqi District": "435",
				"Qingshui District": "436",
				"Dajia District": "437",
				"Waipu District": "438",
				"Da'an District": "439"
			},
			"Changhua County": {
				"Changhua City": "500",
				"Fenyuan Township": "502",
				"Huatan Township": "503",
				"Xiushui Township": "504",
				"Lukang Township": "505",
				"Fuxing Township": "506",
				"Xianxi Township": "507",
				"Hemei Township": "508",
				"Shengang Township": "509",
				"Yuanlin City": "510",
				"Shetou Township": "511",
				"Yongjing Township": "512",
				"Puxin Township": "513",
				"Xihu Township": "514",
				"Dacun Township": "515",
				"Puyan Township": "516",
				"Tianzhong Township": "520",
				"Beidou Township": "521",
				"Tianwei Township": "522",
				"Pitou Township": "523",
				"Xizhou Township": "524",
				"Zhutang Township": "525",
				"Erlin Township": "526",
				"Dacheng Township": "527",
				"Fangyuan Township": "528",
				"Ershui Township": "530"
			},
			"Nantou County": {
				"Nantou City": "540",
				"Zhongliao Township": "541",
				"Caotun Township": "542",
				"Guoxing Township": "544",
				"Puli Township": "545",
				"Ren’ai Township": "546",
				"Mingjian Township": "551",
				"Jiji Township": "552",
				"Shuili Township": "553",
				"Yuchi Township": "555",
				"Xinyi Township": "556",
				"Zhushan Township": "557",
				"Lugu Township": "558"
			},
			"Chiayi City": {
				"East District": "600",
				"West District": "600"
			},
			"Chiayi County": {
				"Fanlu Township": "602",
				"Meishan Township": "603",
				"Zhuqi Township": "604",
				"Alishan Township": "605",
				"Zhongpu Township": "606",
				"Dapu Township": "607",
				"Shuishang Township": "608",
				"Lucao Township": "611",
				"Taibao City": "612",
				"Puzi City": "613",
				"Dongshi Township": "614",
				"Liujiao Township": "615",
				"Xingang Township": "616",
				"Minxiong Township": "621",
				"Dalin Township": "622",
				"Xikou Township": "623",
				"Yizhu Township": "624",
				"Budai Township": "625"
			},
			"Yunlin County": {
				"Dounan Township": "630",
				"Dapi Township": "631",
				"Huwei Township": "632",
				"Tuku Township": "633",
				"Baozhong Township": "634",
				"Dongshi Township": "635",
				"Taixi Township": "636",
				"Lunbei Township": "637",
				"Mailiao Township": "638",
				"Douliu City": "640",
				"Linnei Township": "643",
				"Gukeng Township": "646",
				"Citong Township": "647",
				"Xiluo Township": "648",
				"Erlun Township": "649",
				"Beigang Township": "651",
				"Shuilin Township": "652",
				"Kouhu Township": "653",
				"Sihu Township": "654",
				"Yuanchang Township": "655"
			},
			"Tainan City": {
				"West Central District": "700",
				"East District": "701",
				"South District": "702",
				"North District": "704",
				"Anping District": "708",
				"Annan District": "709",
				"Yongkang District": "710",
				"Guiren District": "711",
				"Xinhua District": "712",
				"Zuozhen District": "713",
				"Yujing District": "714",
				"Nanxi District": "715",
				"Nanhua District": "716",
				"Rende District": "717",
				"Guanmiao District": "718",
				"Longqi District": "719",
				"Guantian District": "720",
				"Madou District": "721",
				"Jiali District": "722",
				"Xigang District": "723",
				"Qigu District": "724",
				"Jiangjun District": "725",
				"Xuejia District": "726",
				"Beimen District": "727",
				"Xinying District": "730",
				"Houbi District": "731",
				"Baihe District": "732",
				"Dongshan District": "733",
				"Liujia District": "734",
				"Xiaying District": "735",
				"Liuying District": "736",
				"Yanshui District": "737",
				"Shanhua District": "741",
				"Danei District": "742",
				"Shanshang District": "743",
				"Xinshi District": "744",
				"Anding District": "745"
			},
			"Kaohsiung City": {
				"Xinxing District": "800",
				"Qianjin District": "801",
				"Lingya District": "802",
				"Yancheng District": "803",
				"Gushan District": "804",
				"Qijin District": "805",
				"Qianzhen District": "806",
				"Sanmin District": "807",
				"Nanzi District": "811",
				"Xiaogang District": "812",
				"Zuoying District": "813",
				"Renwu District": "814",
				"Dashe District": "815",
				"Gangshan District": "820",
				"Luzhu District": "821",
				"Alian District": "822",
				"Tianliao District": "823",
				"Yanchao District": "824",
				"Qiaotou District": "825",
				"Ziguan District": "826",
				"Mituo District": "827",
				"Yong'an District": "828",
				"Hunei District": "829",
				"Fengshan District": "830",
				"Daliao District": "831",
				"Linyuan District": "832",
				"Niaosong District": "833",
				"Dashu District": "840",
				"Qishan District": "842",
				"Meinong District": "843",
				"Liugui District": "844",
				"Neimen District": "845",
				"Shanlin District": "846",
				"Jiaxian District": "847",
				"Taoyuan District": "848",
				"Namaxia District": "849",
				"Maolin District": "851",
				"Jiading District": "852"
			},
			"Pingtung County": {
				"Pingtung City": "900",
				"Sandimen Township": "901",
				"Wutai Township": "902",
				"Majia Township": "903",
				"Jiuru Township": "904",
				"Ligang Township": "905",
				"Gaoshu Township": "906",
				"Yanpu Township": "907",
				"Changzhi Township": "908",
				"Linluo Township": "909",
				"Zhutian Township": "911",
				"Neipu Township": "912",
				"Wandan Township": "913",
				"Chaozhou Township": "920",
				"Taiwu Township": "921",
				"Laiyi Township": "922",
				"Wanluan Township": "923",
				"Kanding Township": "924",
				"Xinpi Township": "925",
				"Nanzhou Township": "926",
				"Linbian Township": "927",
				"Donggang Township": "928",
				"Liuqiu Township": "929",
				"Jiadong Township": "931",
				"Xinyuan Township": "932",
				"Fangliao Township": "940",
				"Fangshan Township": "941",
				"Chunri Township": "942",
				"Shizi Township": "943",
				"Checheng Township": "944",
				"Mudan Township": "945",
				"Hengchun Township": "946",
				"Manzhou Township": "947"
			},
			"Taitung County": {
				"Taitung City": "950",
				"Green Island": "951",
				"Lanyu Township": "952",
				"Yanping Township": "953",
				"Beinan Township": "954",
				"Luye Township": "955",
				"Guanshan Township": "956",
				"Haiduan Township": "957",
				"Chishang Township": "958",
				"Donghe Township": "959",
				"Chenggong Township": "961",
				"Changbin Township": "962",
				"Taimali Township": "963",
				"Jinfeng Township": "964",
				"Dawu Township": "965",
				"Daren Township": "966"
			},
			"Hualien County": {
				"Hualien City": "970",
				"Xincheng Township": "971",
				"Xiulin Township": "972",
				"Ji’an Township": "973",
				"Shoufeng Township": "974",
				"Fenglin Township": "975",
				"Guangfu Township": "976",
				"Fengbin Township": "977",
				"Ruisui Township": "978",
				"Wanrong Township": "979",
				"Yuli Township": "981",
				"Zhuoxi Township": "982",
				"Fuli Township": "983"
			},
			"Kinmen County": {
				"Jinsha Township": "890",
				"Jinhu Township": "891",
				"Jinning Township": "892",
				"Jincheng Township": "893",
				"Lieyu Township": "894",
				"Wuqiu Township": "896"
			},
			"Lianjiang": {
				"Nangan Township": "209",
				"Beigan Township": "210",
				"Juguang Township": "211",
				"Dongyin Island": "212"
			},
			"Penghu County": {
				"Magong City": "880",
				"Xiyu Township": "881",
				"Wang-an Township": "882",
				"Qimei Township": "883",
				"Baisha Township": "884",
				"Huxi Township": "885"
			}
		}
	},
	hkData: {
		"Hong Kong Island": {
			"chi": "香港島",
			"data": {
				"Central and Western": "中西區",
				"Wan Chai": "灣仔區",
				"Eastern": "東區",
				"Southern": "南區"
			}
		},
		"Kowloon": {
			"chi": "九龍",
			"data": {
				"Yau Tsim Mong": "油尖旺區",
				"Sham Shui Po": "深水埗區",
				"Kowloon City": "九龍城區",
				"Wong Tai Sin": "黃大仙區",
				"Kwun Tong": "觀塘區"
			}
		},
		"New Territories": {
			"chi": "新界",
			"data": {
				"Kwai Tsing": "葵青區",
				"Tsuen Wan": "荃灣區",
				"Tuen Mun": "屯門區",
				"Yuen Long": "元朗區",
				"North": "北區",
				"Tai Po": "大埔區",
				"Sha Tin": "沙田區",
				"Sai Kung": "西貢區",
				"Islands": "離島區"
			}
		}
	}
  };

  

var countrySelect = jQuery(document.createElement('div'));
countrySelect.addClass('en__field__element en__field__element--select en__field--country');
countrySelect.html('<label for="en__field_supporter_country_html" class="en__field__label" style="">' + donationLexicon.countryLabel + '</label>');
var countrySelectOptions = jQuery(document.createElement('select'));
jQuery(countrySelectOptions).addClass('js-country-select en__field__input en__field__input--select');
jQuery.each( geo_data.countryData.chi, function( i, item ) {
	
	if ( NRO && 'TW' == NRO && i == 'TW' ) var o = new Option(item, i, true);
	else if ( NRO && 'HK' == NRO && i == 'HK' ) var o = new Option(item, i, true);
	else var o = new Option(item, i);
	/// jquerify the DOM object 'o' so we can use the html method
	jQuery(o).html(item);

	// if ( NRO && 'TW' == NRO ) jQuery(o).prop('selected', true);
	countrySelectOptions.append(o);
});
countrySelect.append(countrySelectOptions);

jQuery(countrySelect).insertBefore('input[name="supporter.country"]');

jQuery(document).on('change',".js-country-select", function(){
	jQuery('input[name="supporter.country"]').val(this.value);
	if ( this.value == 'HK' ) {
		jQuery('.en__field--city').show();
		jQuery('.en__field--region').show();
		var o = new Option(donationLexicon.cityLabel, '');
		jQuery(o).html(donationLexicon.cityLabel);
		jQuery('.js-city-select').html(o);
		jQuery.each( geo_data.hkData, function( i, item ) {
			var o = new Option(item.chi, i);
			jQuery(o).html(item.chi);
			jQuery('.js-city-select').append(o);
		});
		regexPhone = window.NRO_PROPERTIES['HK'].regex.phone;
		regexPhoneMessage = window.NRO_PROPERTIES['HK'].validation.format_phone;
		// citySelect.append(citySelectOptions);

	} else if ( this.value == 'TW' ) {
		jQuery('.en__field--city').show();
		jQuery('.en__field--region').show();
		var o = new Option(donationLexicon.cityLabel, '');
		jQuery(o).html(donationLexicon.cityLabel);
		jQuery('.js-city-select').html(o);
		jQuery.each( geo_data.twData.chi, function( i, item ) {
			var o = new Option(i, i);
			jQuery(o).html(i);
			jQuery('.js-city-select').append(o);
		});
		regexPhone = window.NRO_PROPERTIES['TW'].regex.phone;
		regexPhoneMessage = window.NRO_PROPERTIES['TW'].validation.format_phone;
		// citySelect.append(citySelectOptions);

	} else {
		jQuery('input[name="supporter.city"]').val('-');
		jQuery('input[name="supporter.region"]').val('-');
		jQuery('input[name="supporter.postcode"]').val('00000');
		jQuery('.en__field--city').hide();
		jQuery('.en__field--region').hide();
		var o = new Option('-', '-', true, true);
		jQuery(o).html('-');
		jQuery('.js-city-select').html(o);

		var o2 = new Option('-', '-', true, true);
		jQuery(o2).html('-');
		jQuery('.js-region-select').html(o2);
		regexPhone = window.NRO_PROPERTIES[NRO].regex.phone_general;
		regexPhoneMessage = window.NRO_PROPERTIES[NRO].validation.format_phone_general;
	}
});  


var citySelect = jQuery(document.createElement('div'));
citySelect.addClass('en__field__element en__field__element--select en__field--city');
citySelect.html('<label for="en__field_supporter_region_html" class="en__field__label" style="">' + donationLexicon.cityLabel + '</label>');
var citySelectOptions = jQuery(document.createElement('select'));
jQuery(citySelectOptions).addClass('js-city-select en__field__input en__field__input--select');
var o = new Option(donationLexicon.cityLabel, '');
jQuery(o).html(donationLexicon.cityLabel);
citySelectOptions.append(o);

if ( NRO && 'TW' == NRO ) {
	jQuery.each( geo_data.twData.chi, function( i, item ) {
		var o = new Option(i, i);
		jQuery(o).html(i);
		citySelectOptions.append(o);
	});
} else if ( NRO && 'HK' == NRO ) {
	jQuery.each( geo_data.hkData, function( i, item ) {
		// console.log(i);
		// console.log(item);
		var o = new Option(item.chi, i);
		jQuery(o).html(item.chi);
		citySelectOptions.append(o);
	});
}

citySelect.append(citySelectOptions);
jQuery(citySelect).insertBefore('input[name="supporter.country"]');

jQuery(document).on('change',".js-city-select", function(){
		var indexCity = this.value;
		jQuery('input[name="supporter.city"]').val(this.value);
		// console.log(indexCity);
		var o = new Option(donationLexicon.cityLabel, '');
		jQuery(o).html(donationLexicon.regionLabel);
		jQuery('.js-region-select').html(o);
		
		if (jQuery('.js-country-select').val() == 'HK' ) {
		
			jQuery.each( geo_data.hkData[indexCity].data, function( i, item ) {
				// console.log(i);
				// console.log(item);
				var o = new Option(item, i);
				jQuery(o).html(item);
				jQuery('.js-region-select').append(o);
			});
			// citySelect.append(citySelectOptions);
		} else if (jQuery('.js-country-select').val() == 'TW' ) {
		
			jQuery.each( geo_data.twData.chi[indexCity], function( i, item ) {
				// console.log(i);
				// console.log(item);
				var o = new Option(i, i);
				jQuery(o).html(i);
				jQuery('.js-region-select').append(o);
			});
			// citySelect.append(citySelectOptions);
		} 
});

var regionSelect = jQuery(document.createElement('div'));
regionSelect.addClass('en__field__element en__field__element--select en__field--region');
regionSelect.html('<label for="en__field_supporter_region_html" class="en__field__label" style="">' + donationLexicon.regionLabel + '</label>');
var regionSelectOptions = jQuery(document.createElement('select'));
jQuery(regionSelectOptions).addClass('js-region-select en__field__input en__field__input--select');
var o = new Option(donationLexicon.regionLabel, '');
jQuery(o).html(donationLexicon.regionLabel);
regionSelectOptions.append(o);
/*
jQuery.each( geo_data.hkData, function( i, item ) {
	// console.log(i);
	// console.log(item);
	var o = new Option(item.chi, item.chi);
	jQuery(o).html(item.chi);
	regionSelectOptions.append(o);
});
*/
regionSelect.append(regionSelectOptions);
jQuery(regionSelect).insertBefore('input[name="supporter.country"]');

jQuery(document).on('change',".js-region-select", function(){
	jQuery('input[name="supporter.region"]').val(this.value);
	if (jQuery('.js-country-select').val() == 'TW' ) {
		var indexCity = jQuery('.js-city-select').val();
		var indexRegion = this.value;
		jQuery('input[name="supporter.postcode"]').val(geo_data.twData.chi[indexCity][indexRegion]);
	} else {
		jQuery('input[name="supporter.postcode"]').val('00000');
	}
});
  
}
