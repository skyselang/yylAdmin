<?php
// +----------------------------------------------------------------------
// | yylAdmin 前后分离，简单轻量，免费开源，开箱即用，极简后台管理系统
// +----------------------------------------------------------------------
// | Copyright https://gitee.com/skyselang All rights reserved
// +----------------------------------------------------------------------
// | Gitee: https://gitee.com/skyselang/yylAdmin
// +----------------------------------------------------------------------

namespace app\common\utils;

use app\common\cache\utils\CaptchaCache as Cache;

/**
 * 验证码
 */
class CaptchaUtils
{
    // 验证码开关
    protected static $switch = false;
    // 验证码类型：1数字，2字母，3数字字母，4算术，5中文
    protected static $type = 1;
    // 验证码配置
    protected static $config = null;
    // 验证码图片实例
    private static $im = null;
    // 验证码字体颜色
    private static $color = null;
    // 验证码字符集合
    protected static $codeSet = '2345678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
    // 验证码过期时间（s）
    protected static $expire = 180;
    // 使用中文验证码
    protected static $useZh = false;
    // 中文验证码字符串
    protected static $zhSet = '们以我到他会作时要动国产的一是工就年阶义发成部民可出能方进在了不和有大这主中人上为来分生对于学下级地个用同行面说种过命度革而多子后自社加小机也经力线本电高量长党得实家定深法表着水理化争现所二起政三好十战无农使性前等反体合斗路图把结第里正新开论之物从当两些还天资事队批点育重其思与间内去因件日利相由压员气业代全组数果期导平各基或月毛然如应形想制心样干都向变关问比展那它最及外没看治提五解系林者米群头意只明四道马认次文通但条较克又公孔领军流入接席位情运器并飞原油放立题质指建区验活众很教决特此常石强极土少已根共直团统式转别造切九你取西持总料连任志观调七么山程百报更见必真保热委手改管处己将修支识病象几先老光专什六型具示复安带每东增则完风回南广劳轮科北打积车计给节做务被整联步类集号列温装即毫知轴研单色坚据速防史拉世设达尔场织历花受求传口断况采精金界品判参层止边清至万确究书术状厂须离再目海交权且儿青才证低越际八试规斯近注办布门铁需走议县兵固除般引齿千胜细影济白格效置推空配刀叶率述今选养德话查差半敌始片施响收华觉备名红续均药标记难存测士身紧液派准斤角降维板许破述技消底床田势端感往神便贺村构照容非搞亚磨族火段算适讲按值美态黄易彪服早班麦削信排台声该击素张密害侯草何树肥继右属市严径螺检左页抗苏显苦英快称坏移约巴材省黑武培著河帝仅针怎植京助升王眼她抓含苗副杂普谈围食射源例致酸旧却充足短划剂宣环落首尺波承粉践府鱼随考刻靠够满夫失包住促枝局菌杆周护岩师举曲春元超负砂封换太模贫减阳扬江析亩木言球朝医校古呢稻宋听唯输滑站另卫字鼓刚写刘微略范供阿块某功套友限项余倒卷创律雨让骨远帮初皮播优占死毒圈伟季训控激找叫云互跟裂粮粒母练塞钢顶策双留误础吸阻故寸盾晚丝女散焊功株亲院冷彻弹错散商视艺灭版烈零室轻血倍缺厘泵察绝富城冲喷壤简否柱李望盘磁雄似困巩益洲脱投送奴侧润盖挥距触星松送获兴独官混纪依未突架宽冬章湿偏纹吃执阀矿寨责熟稳夺硬价努翻奇甲预职评读背协损棉侵灰虽矛厚罗泥辟告卵箱掌氧恩爱停曾溶营终纲孟钱待尽俄缩沙退陈讨奋械载胞幼哪剥迫旋征槽倒握担仍呀鲜吧卡粗介钻逐弱脚怕盐末阴丰雾冠丙街莱贝辐肠付吉渗瑞惊顿挤秒悬姆烂森糖圣凹陶词迟蚕亿矩康遵牧遭幅园腔订香肉弟屋敏恢忘编印蜂急拿扩伤飞露核缘游振操央伍域甚迅辉异序免纸夜乡久隶缸夹念兰映沟乙吗儒杀汽磷艰晶插埃燃欢铁补咱芽永瓦倾阵碳演威附牙芽永瓦斜灌欧献顺猪洋腐请透司危括脉宜笑若尾束壮暴企菜穗楚汉愈绿拖牛份染既秋遍锻玉夏疗尖殖井费州访吹荣铜沿替滚客召旱悟刺脑措贯藏敢令隙炉壳硫煤迎铸粘探临薄旬善福纵择礼愿伏残雷延烟句纯渐耕跑泽慢栽鲁赤繁境潮横掉锥希池败船假亮谓托伙哲怀割摆贡呈劲财仪沉炼麻罪祖息车穿货销齐鼠抽画饲龙库守筑房歌寒喜哥洗蚀废纳腹乎录镜妇恶脂庄擦险赞钟摇典柄辩竹谷卖乱虚桥奥伯赶垂途额壁网截野遗静谋弄挂课镇妄盛耐援扎虑键归符庆聚绕摩忙舞遇索顾胶羊湖钉仁音迹碎伸灯避泛亡答勇频皇柳哈揭甘诺概宪浓岛袭谁洪谢炮浇斑讯懂灵蛋闭孩释乳巨徒私银伊景坦累匀霉杜乐勒隔弯绩招绍胡呼痛峰零柴簧午跳居尚丁秦稍追梁折耗碱殊岗挖氏刃剧堆赫荷胸衡勤膜篇登驻案刊秧缓凸役剪川雪链渔啦脸户洛孢勃盟买杨宗焦赛旗滤硅炭股坐蒸凝竟陷枪黎救冒暗洞犯筒您宋弧爆谬涂味津臂障褐陆啊健尊豆拔莫抵桑坡缝警挑污冰柬嘴啥饭塑寄赵喊垫丹渡耳刨虎笔稀昆浪萨茶滴浅拥穴覆伦娘吨浸袖珠雌妈紫戏塔锤震岁貌洁剖牢锋疑霸闪埔猛诉刷狠忽灾闹乔唐漏闻沈熔氯荒茎男凡抢像浆旁玻亦忠唱蒙予纷捕锁尤乘乌智淡允叛畜俘摸锈扫毕璃宝芯爷鉴秘净蒋钙肩腾枯抛轨堂拌爸循诱祝励肯酒绳穷塘燥泡袋朗喂铝软渠颗惯贸粪综墙趋彼届墨碍启逆卸航衣孙龄岭骗休借';
    // 使用背景图片
    protected static $useImgBg = false;
    // 验证码字体大小(px)
    protected static $fontSize = 26;
    // 是否画混淆曲线
    protected static $useCurve = true;
    // 是否添加杂点
    protected static $useNoise = true;
    // 验证码图片高度
    protected static $imageH = 0;
    // 验证码图片宽度
    protected static $imageW = 0;
    // 验证码位数
    protected static $length = 5;
    // 验证码字体，不设置随机获取
    protected static $fontttf = '';
    // 验证码背景颜色
    protected static $bg = [243, 251, 254];
    // 算术验证码
    protected static $math = false;
    // 算术验证码类型：1加，2减，3乘，4除
    protected static $math_type = [1, 2, 3, 4];
    // 字体图片资源路径
    protected static $assets_path = '';
    // 是否使用透明背景
    protected static $transparent = true;

    /**
     * 验证码配置
     * @param int $type 验证码类型：1数字，2字母，3数字字母，4算术，5中文
     */
    protected static function configure($type = 1)
    {
        $config = config('captcha', []);
        foreach ($config as $key => $val) {
            if (property_exists(__CLASS__, $key)) {
                self::${$key} = $val;
            }
        }

        if ($type == 1) {
            self::$codeSet = '0123456789';
        } elseif ($type == 2) {
            self::$codeSet = 'abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
        } elseif ($type == 3) {
            self::$codeSet = '123456789abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
        } elseif ($type == 4) {
            self::$math = true;
        } elseif ($type == 5) {
            self::$useZh = true;
        } else {
            self::$codeSet = '0123456789';
        }

        self::$assets_path = root_path() . '/private/captcha/assets/';
    }

    /**
     * 验证码类型
     */
    public static function types()
    {
        $types = [
            ['value' => 1, 'label' => lang('数字')],
            ['value' => 2, 'label' => lang('字母')],
            ['value' => 3, 'label' => lang('数字字母')],
            ['value' => 4, 'label' => lang('算术')],
            ['value' => 5, 'label' => lang('中文')],
        ];

        return $types;
    }

    /**
     * 验证码生成
     * @param int $type 验证码类型：1数字，2字母，3数字字母，4算术，5中文
     */
    public static function create($type = 1)
    {
        self::configure($type);

        $switch = self::$switch;
        if (empty($switch)) {
            $captcha['captcha_switch'] = $switch;
            $captcha['captcha_id']     = '';
            $captcha['captcha_img']    = '';

            return $captcha;
        }

        $generator = self::generate();

        // 图片宽(px)
        self::$imageW || self::$imageW = self::$length * self::$fontSize * 1.5 + self::$length * self::$fontSize / 2;
        // 图片高(px)
        self::$imageH || self::$imageH = self::$fontSize * 2.5;
        // 建立一幅 self::$imageW x self::$imageH 的图像
        if (self::$transparent) {
            // 创建真彩色图像以支持透明度
            self::$im = imagecreatetruecolor(self::$imageW, self::$imageH);
            // 启用 alpha 通道
            imagesavealpha(self::$im, true);
            // 设置透明背景
            $transparent = imagecolorallocatealpha(self::$im, 0, 0, 0, 127);
            imagefill(self::$im, 0, 0, $transparent);
        } else {
            // 创建普通图像
            self::$im = imagecreate(self::$imageW, self::$imageH);
            // 设置背景颜色
            imagecolorallocate(self::$im, self::$bg[0], self::$bg[1], self::$bg[2]);
        }
        // 验证码字体随机颜色
        if (self::$transparent) {
            // 透明背景时使用不透明的颜色
            self::$color = imagecolorallocate(self::$im, mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150));
        } else {
            self::$color = imagecolorallocate(self::$im, mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150));
        }
        // 验证码使用随机字体
        $ttfPath = self::$assets_path . (self::$useZh ? 'zhttfs' : 'ttfs') . '/';

        if (empty(self::$fontttf)) {
            $dir  = dir($ttfPath);
            $ttfs = [];
            while (false !== ($file = $dir->read())) {
                if (substr($file, -4) == '.ttf') {
                    $ttfs[] = $file;
                }
            }
            $dir->close();
            self::$fontttf = $ttfs[array_rand($ttfs)];
        }

        $fontttf = $ttfPath . self::$fontttf;

        if (self::$useImgBg && !self::$transparent) {
            // 绘背景图片（透明背景时不使用背景图片）
            self::background();
        }

        if (self::$useNoise) {
            // 绘杂点
            self::writeNoise();
        }
        if (self::$useCurve) {
            // 绘干扰线
            self::writeCurve();
        }

        // 绘验证码
        $text = self::$useZh ? preg_split('/(?<!^)(?!$)/u', $generator['val']) : str_split($generator['val']); // 验证码
        if (count($text) > 10) {
            foreach ($text as $k => $v) {
                if (trim($v) === '') {
                    unset($text[$k]);
                }
            }
        }
        $text = array_values($text);
        foreach ($text as $index => $char) {
            $x     = self::$fontSize * ($index + 1) * mt_rand(1, 1) * (self::$math ? 1 : 1.5);
            $y     = self::$fontSize + mt_rand(10, 20);
            $angle = self::$math ? 0 : mt_rand(-40, 40);

            imagettftext(self::$im, self::$fontSize, $angle, $x, $y, self::$color, $fontttf, $char);
        }
        ob_start();
        // 输出图像
        $tmpfname = tempnam(sys_get_temp_dir(), 'ya');
        imagepng(self::$im, $tmpfname);
        $img_data = file_get_contents($tmpfname);
        $img_base64 = 'data:image/png;base64,' . base64_encode($img_data);
        ob_get_clean();
        try {
            unlink($tmpfname);
        } catch (\Exception $e) {
        }
        imagedestroy(self::$im);

        $captcha['captcha_switch'] = $switch;
        $captcha['captcha_id']     = $generator['key'];
        $captcha['captcha_img']    = $img_base64;

        return $captcha;
    }

    /**
     * 验证码创建
     * 并把验证码保存到缓存中
     */
    protected static function generate()
    {
        $key = uniqids();
        $bag = '';

        if (self::$math) {
            self::$useZh  = false;
            self::$length = 5;

            $math_types = self::$math_type;
            if (empty($math_types)) {
                $math_types = [1];
            }
            $math_key  = mt_rand(0, count($math_types) - 1);
            $math_type = $math_types[$math_key] ?? 1;
            if ($math_type == 2) {
                $x   = mt_rand(11, 39);
                $y   = mt_rand(0, 10);
                $bag = "{$x} - {$y} = ";
                $val = $x - $y;
            } elseif ($math_type == 3) {
                $r   = mt_rand(0, 1);
                $x   = $r ? mt_rand(1, 11) :  mt_rand(1000, 9999);
                $y   = $r ? mt_rand(1, 10) : 1;
                $bag = "{$x} x {$y} = ";
                $val = $x * $y;
            } elseif ($math_type == 4) {
                $r   = mt_rand(0, 1);
                $x   = $r ? mt_rand(1, 11) : mt_rand(1000, 9999);
                $y   = $r ? mt_rand(1, 9) : 1;
                $j   = $x * $y;
                $bag = "{$j} / {$y} = ";
                $val = $j / $y;
            } else {
                $x   = mt_rand(1, 39);
                $y   = mt_rand(0, 9);
                $bag = mt_rand(0, 1) ? "{$x} + {$y} = " : "{$y} + {$x} = ";
                $val = $x + $y;
            }
        } else {
            if (self::$useZh) {
                $characters = preg_split('/(?<!^)(?!$)/u', self::$zhSet);
            } else {
                $characters = str_split(self::$codeSet);
            }

            for ($i = 0; $i < self::$length; $i++) {
                $bag .= $characters[rand(0, count($characters) - 1)];
            }

            $val = mb_strtolower($bag, 'UTF-8');
        }
        $cache = new Cache();
        $cache->set($key, $val, self::$expire);

        return ['key' => $key, 'val' => $bag];
    }

    /**
     * 验证码验证
     * @param string $captcha_id   验证码id
     * @param string $captcha_code 验证码
     */
    public static function check($captcha_id, $captcha_code)
    {
        self::configure();

        $switch = self::$switch;
        if (empty($switch)) {
            return true;
        }

        $cache   = new Cache();
        $captcha = $cache->get($captcha_id);
        if ($captcha && ($captcha == $captcha_code)) {
            $cache->del($captcha_id);
            return true;
        }

        return false;
    }

    /**
     * 画一条由两条连在一起构成的随机正弦函数曲线作干扰线
     *
     * 高中的数学公式
     * 正弦型函数解析式：y=Asin(ωx+φ)+b
     * 各常数值对函数图像的影响：
     * A：决定峰值（即纵向拉伸压缩的倍数）
     * b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）
     * φ：决定波形与X轴位置关系或横向移动距离（左加右减）
     * ω：决定周期（最小正周期T=2π/∣ω∣）
     */
    protected static function writeCurve()
    {
        $px = $py = 0;

        // 曲线前部分
        $A = mt_rand(1, self::$imageH / 2); // 振幅
        $b = mt_rand(-self::$imageH / 4, self::$imageH / 4); // Y轴方向偏移量
        $f = mt_rand(-self::$imageH / 4, self::$imageH / 4); // X轴方向偏移量
        $T = mt_rand(self::$imageH, self::$imageW * 2); // 周期
        $w = (2 * M_PI) / $T;

        $px1 = 0; // 曲线横坐标起始位置
        $px2 = mt_rand(self::$imageW / 2, self::$imageW * 0.8); // 曲线横坐标结束位置

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + self::$imageH / 2; // y = Asin(ωx+φ) + b
                $i  = (int) (self::$fontSize / 5);
                while ($i > 0) {
                    // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多
                    if (self::$transparent) {
                        // 透明背景时使用半透明干扰线
                        $curveColor = imagecolorallocatealpha(self::$im, mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150), mt_rand(30, 80));
                        imagesetpixel(self::$im, $px + $i, $py + $i, $curveColor);
                    } else {
                        imagesetpixel(self::$im, $px + $i, $py + $i, self::$color);
                    }
                    $i--;
                }
            }
        }

        // 曲线后部分
        $A   = mt_rand(1, self::$imageH / 2); // 振幅
        $f   = mt_rand(-self::$imageH / 4, self::$imageH / 4); // X轴方向偏移量
        $T   = mt_rand(self::$imageH, self::$imageW * 2); // 周期
        $w   = (2 * M_PI) / $T;
        $b   = $py - $A * sin($w * $px + $f) - self::$imageH / 2;
        $px1 = $px2;
        $px2 = self::$imageW;

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + self::$imageH / 2; // y = Asin(ωx+φ) + b
                $i  = (int) (self::$fontSize / 5);
                            while ($i > 0) {
                if (self::$transparent) {
                    // 透明背景时使用半透明干扰线
                    $curveColor = imagecolorallocatealpha(self::$im, mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150), mt_rand(30, 80));
                    imagesetpixel(self::$im, $px + $i, $py + $i, $curveColor);
                } else {
                    imagesetpixel(self::$im, $px + $i, $py + $i, self::$color);
                }
                $i--;
            }
            }
        }
    }

    /**
     * 画杂点
     * 往图片上写不同颜色的字母或数字
     */
    protected static function writeNoise()
    {
        $codeSet = self::$codeSet;
        $length  = strlen($codeSet) - 1;

        for ($i = 0; $i < 10; $i++) {
            // 杂点颜色
            if (self::$transparent) {
                // 透明背景时使用半透明杂点
                $noiseColor = imagecolorallocatealpha(self::$im, mt_rand(150, 225), mt_rand(150, 225), mt_rand(150, 225), mt_rand(50, 100));
            } else {
                $noiseColor = imagecolorallocate(self::$im, mt_rand(150, 225), mt_rand(150, 225), mt_rand(150, 225));
            }
            for ($j = 0; $j < 5; $j++) {
                // 绘杂点
                imagestring(self::$im, 5, mt_rand(-10, self::$imageW), mt_rand(-10, self::$imageH), $codeSet[mt_rand(0, $length)], $noiseColor);
            }
        }
    }

    /**
     * 绘制背景图片
     * 注：如果验证码输出图片比较大，将占用比较多的系统资源
     */
    protected static function background()
    {
        $path = self::$assets_path . 'bgs/';
        $dir  = dir($path);

        $bgs = [];
        while (false !== ($file = $dir->read())) {
            if ('.' != $file[0] && substr($file, -4) == '.jpg') {
                $bgs[] = $path . $file;
            }
        }
        $dir->close();

        $gb = $bgs[array_rand($bgs)];

        try {
            list($width, $height) = getimagesize($gb);
            // 重采样
            $bgImage = imagecreatefromjpeg($gb);
            imagecopyresampled(self::$im, $bgImage, 0, 0, 0, 0, self::$imageW, self::$imageH, $width, $height);
            imagedestroy($bgImage);
        } catch (\Exception $e) {
        }
    }
}
