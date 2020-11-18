<?php

namespace App\Models\Css;

use Illuminate\Support\Arr;

class CssLine
{

    const COLOR_MAP = [
        [
            'name' => 'lightsalmon',
            'hex'  => '#FFA07A',
            'rgb'  => 'rgb(255,160,122)',
        ],
        [
            'name' => 'salmon',
            'hex'  => '#FA8072',
            'rgb'  => 'rgb(250,128,114)',
        ],
        [
            'name' => 'darksalmon',
            'hex'  => '#E9967A',
            'rgb'  => 'rgb(233,150,122)',
        ],
        [
            'name' => 'lightcoral',
            'hex'  => '#F08080',
            'rgb'  => 'rgb(240,128,128)',
        ],
        [
            'name' => 'indianred',
            'hex'  => '#CD5C5C',
            'rgb'  => 'rgb(205,92,92)',
        ],
        [
            'name' => 'crimson',
            'hex'  => '#DC143C',
            'rgb'  => 'rgb(220,20,60)',
        ],
        [
            'name' => 'firebrick',
            'hex'  => '#B22222',
            'rgb'  => 'rgb(178,34,34)',
        ],
        [
            'name' => 'red',
            'hex'  => '#FF0000',
            'rgb'  => 'rgb(255,0,0)',
        ],
        [
            'name' => 'darkred',
            'hex'  => '#8B0000',
            'rgb'  => 'rgb(139,0,0)',
        ],
        [
            'name' => 'coral',
            'hex'  => '#FF7F50',
            'rgb'  => 'rgb(255,127,80)',
        ],
        [
            'name' => 'tomato',
            'hex'  => '#FF6347',
            'rgb'  => 'rgb(255,99,71)',
        ],
        [
            'name' => 'orangered',
            'hex'  => '#FF4500',
            'rgb'  => 'rgb(255,69,0)',
        ],
        [
            'name' => 'gold',
            'hex'  => '#FFD700',
            'rgb'  => 'rgb(255,215,0)',
        ],
        [
            'name' => 'orange',
            'hex'  => '#FFA500',
            'rgb'  => 'rgb(255,165,0)',
        ],
        [
            'name' => 'darkorange',
            'hex'  => '#FF8C00',
            'rgb'  => 'rgb(255,140,0)',
        ],
        [
            'name' => 'lightyellow',
            'hex'  => '#FFFFE0',
            'rgb'  => 'rgb(255,255,224)',
        ],
        [
            'name' => 'lemonchiffon',
            'hex'  => '#FFFACD',
            'rgb'  => 'rgb(255,250,205)',
        ],
        [
            'name' => 'lightgoldenrodyellow',
            'hex'  => '#FAFAD2',
            'rgb'  => 'rgb(250,250,210)',
        ],
        [
            'name' => 'papayawhip',
            'hex'  => '#FFEFD5',
            'rgb'  => 'rgb(255,239,213)',
        ],
        [
            'name' => 'moccasin',
            'hex'  => '#FFE4B5',
            'rgb'  => 'rgb(255,228,181)',
        ],
        [
            'name' => 'peachpuff',
            'hex'  => '#FFDAB9',
            'rgb'  => 'rgb(255,218,185)',
        ],
        [
            'name' => 'palegoldenrod',
            'hex'  => '#EEE8AA',
            'rgb'  => 'rgb(238,232,170)',
        ],
        [
            'name' => 'khaki',
            'hex'  => '#F0E68C',
            'rgb'  => 'rgb(240,230,140)',
        ],
        [
            'name' => 'darkkhaki',
            'hex'  => '#BDB76B',
            'rgb'  => 'rgb(189,183,107)',
        ],
        [
            'name' => 'yellow',
            'hex'  => '#FFFF00',
            'rgb'  => 'rgb(255,255,0)',
        ],
        [
            'name' => 'lawngreen',
            'hex'  => '#7CFC00',
            'rgb'  => 'rgb(124,252,0)',
        ],
        [
            'name' => 'chartreuse',
            'hex'  => '#7FFF00',
            'rgb'  => 'rgb(127,255,0)',
        ],
        [
            'name' => 'limegreen',
            'hex'  => '#32CD32',
            'rgb'  => 'rgb(50,205,50)',
        ],
        [
            'name' => 'lime',
            'hex'  => '#00FF00',
            'rgb'  => 'rgb(0.255.0)',
        ],
        [
            'name' => 'forestgreen',
            'hex'  => '#228B22',
            'rgb'  => 'rgb(34,139,34)',
        ],
        [
            'name' => 'green',
            'hex'  => '#008000',
            'rgb'  => 'rgb(0,128,0)',
        ],
        [
            'name' => 'darkgreen',
            'hex'  => '#006400',
            'rgb'  => 'rgb(0,100,0)',
        ],
        [
            'name' => 'greenyellow',
            'hex'  => '#ADFF2F',
            'rgb'  => 'rgb(173,255,47)',
        ],
        [
            'name' => 'yellowgreen',
            'hex'  => '#9ACD32',
            'rgb'  => 'rgb(154,205,50)',
        ],
        [
            'name' => 'springgreen',
            'hex'  => '#00FF7F',
            'rgb'  => 'rgb(0,255,127)',
        ],
        [
            'name' => 'mediumspringgreen',
            'hex'  => '#00FA9A',
            'rgb'  => 'rgb(0,250,154)',
        ],
        [
            'name' => 'lightgreen',
            'hex'  => '#90EE90',
            'rgb'  => 'rgb(144,238,144)',
        ],
        [
            'name' => 'palegreen',
            'hex'  => '#98FB98',
            'rgb'  => 'rgb(152,251,152)',
        ],
        [
            'name' => 'darkseagreen',
            'hex'  => '#8FBC8F',
            'rgb'  => 'rgb(143,188,143)',
        ],
        [
            'name' => 'mediumseagreen',
            'hex'  => '#3CB371',
            'rgb'  => 'rgb(60,179,113)',
        ],
        [
            'name' => 'seagreen',
            'hex'  => '#2E8B57',
            'rgb'  => 'rgb(46,139,87)',
        ],
        [
            'name' => 'olive',
            'hex'  => '#808000',
            'rgb'  => 'rgb(128,128,0)',
        ],
        [
            'name' => 'darkolivegreen',
            'hex'  => '#556B2F',
            'rgb'  => 'rgb(85,107,47)',
        ],
        [
            'name' => 'olivedrab',
            'hex'  => '#6B8E23',
            'rgb'  => 'rgb(107,142,35)',
        ],
        [
            'name' => 'lightcyan',
            'hex'  => '#E0FFFF',
            'rgb'  => 'rgb(224,255,255)',
        ],
        [
            'name' => 'cyan',
            'hex'  => '#00FFFF',
            'rgb'  => 'rgb(0,255,255)',
        ],
        [
            'name' => 'aqua',
            'hex'  => '#00FFFF',
            'rgb'  => 'rgb(0,255,255)',
        ],
        [
            'name' => 'aquamarine',
            'hex'  => '#7FFFD4',
            'rgb'  => 'rgb(127,255,212)',
        ],
        [
            'name' => 'mediumaquamarine',
            'hex'  => '#66CDAA',
            'rgb'  => 'rgb(102,205,170)',
        ],
        [
            'name' => 'paleturquoise',
            'hex'  => '#AFEEEE',
            'rgb'  => 'rgb(175,238,238)',
        ],
        [
            'name' => 'turquoise',
            'hex'  => '#40E0D0',
            'rgb'  => 'rgb(64,224,208)',
        ],
        [
            'name' => 'mediumturquoise',
            'hex'  => '#48D1CC',
            'rgb'  => 'rgb(72,209,204)',
        ],
        [
            'name' => 'darkturquoise',
            'hex'  => '#00CED1',
            'rgb'  => 'rgb(0,206,209)',
        ],
        [
            'name' => 'lightseagreen',
            'hex'  => '#20B2AA',
            'rgb'  => 'rgb(32,178,170)',
        ],
        [
            'name' => 'cadetblue',
            'hex'  => '#5F9EA0',
            'rgb'  => 'rgb(95,158,160)',
        ],
        [
            'name' => 'darkcyan',
            'hex'  => '#008B8B',
            'rgb'  => 'rgb(0,139,139)',
        ],
        [
            'name' => 'teal',
            'hex'  => '#008080',
            'rgb'  => 'rgb(0,128,128)',
        ],
        [
            'name' => 'powderblue',
            'hex'  => '#B0E0E6',
            'rgb'  => 'rgb(176,224,230)',
        ],
        [
            'name' => 'lightblue',
            'hex'  => '#ADD8E6',
            'rgb'  => 'rgb(173,216,230)',
        ],
        [
            'name' => 'lightskyblue',
            'hex'  => '#87CEFA',
            'rgb'  => 'rgb(135,206,250)',
        ],
        [
            'name' => 'skyblue',
            'hex'  => '#87CEEB',
            'rgb'  => 'rgb(135,206,235)',
        ],
        [
            'name' => 'deepskyblue',
            'hex'  => '#00BFFF',
            'rgb'  => 'rgb(0,191,255)',
        ],
        [
            'name' => 'lightsteelblue',
            'hex'  => '#B0C4DE',
            'rgb'  => 'rgb(176,196,222)',
        ],
        [
            'name' => 'dodgerblue',
            'hex'  => '#1E90FF',
            'rgb'  => 'rgb(30,144,255)',
        ],
        [
            'name' => 'cornflowerblue',
            'hex'  => '#6495ED',
            'rgb'  => 'rgb(100,149,237)',
        ],
        [
            'name' => 'steelblue',
            'hex'  => '#4682B4',
            'rgb'  => 'rgb(70,130,180)',
        ],
        [
            'name' => 'royalblue',
            'hex'  => '#4169E1',
            'rgb'  => 'rgb(65,105,225)',
        ],
        [
            'name' => 'blue',
            'hex'  => '#0000FF',
            'rgb'  => 'rgb(0,0,255)',
        ],
        [
            'name' => 'mediumblue',
            'hex'  => '#0000CD',
            'rgb'  => 'rgb(0,0,205)',
        ],
        [
            'name' => 'darkblue',
            'hex'  => '#00008B',
            'rgb'  => 'rgb(0,0,139)',
        ],
        [
            'name' => 'navy',
            'hex'  => '#000080',
            'rgb'  => 'rgb(0,0,128)',
        ],
        [
            'name' => 'midnightblue',
            'hex'  => '#191970',
            'rgb'  => 'rgb(25,25,112)',
        ],
        [
            'name' => 'mediumslateblue',
            'hex'  => '#7B68EE',
            'rgb'  => 'rgb(123,104,238)',
        ],
        [
            'name' => 'slateblue',
            'hex'  => '#6A5ACD',
            'rgb'  => 'rgb(106,90,205)',
        ],
        [
            'name' => 'darkslateblue',
            'hex'  => '#483D8B',
            'rgb'  => 'rgb(72,61,139)',
        ],
        [
            'name' => 'lavender',
            'hex'  => '#E6E6FA',
            'rgb'  => 'rgb(230,230,250)',
        ],
        [
            'name' => 'thistle',
            'hex'  => '#D8BFD8',
            'rgb'  => 'rgb(216,191,216)',
        ],
        [
            'name' => 'plum',
            'hex'  => '#DDA0DD',
            'rgb'  => 'rgb(221,160,221)',
        ],
        [
            'name' => 'violet',
            'hex'  => '#EE82EE',
            'rgb'  => 'rgb(238,130,238)',
        ],
        [
            'name' => 'orchid',
            'hex'  => '#DA70D6',
            'rgb'  => 'rgb(218,112,214)',
        ],
        [
            'name' => 'fuchsia',
            'hex'  => '#FF00FF',
            'rgb'  => 'rgb(255,0,255)',
        ],
        [
            'name' => 'magenta',
            'hex'  => '#FF00FF',
            'rgb'  => 'rgb(255,0,255)',
        ],
        [
            'name' => 'mediumorchid',
            'hex'  => '#BA55D3',
            'rgb'  => 'rgb(186,85,211)',
        ],
        [
            'name' => 'mediumpurple',
            'hex'  => '#9370DB',
            'rgb'  => 'rgb(147,112,219)',
        ],
        [
            'name' => 'blueviolet',
            'hex'  => '#8A2BE2',
            'rgb'  => 'rgb(138,43,226)',
        ],
        [
            'name' => 'darkviolet',
            'hex'  => '#9400D3',
            'rgb'  => 'rgb(148,0,211)',
        ],
        [
            'name' => 'darkorchid',
            'hex'  => '#9932CC',
            'rgb'  => 'rgb(153,50,204)',
        ],
        [
            'name' => 'darkmagenta',
            'hex'  => '#8B008B',
            'rgb'  => 'rgb(139,0,139)',
        ],
        [
            'name' => 'purple',
            'hex'  => '#800080',
            'rgb'  => 'rgb(128,0,128)',
        ],
        [
            'name' => 'indigo',
            'hex'  => '#4B0082',
            'rgb'  => 'rgb(75,0,130)',
        ],
        [
            'name' => 'pink',
            'hex'  => '#FFC0CB',
            'rgb'  => 'rgb(255,192,203)',
        ],
        [
            'name' => 'lightpink',
            'hex'  => '#FFB6C1',
            'rgb'  => 'rgb(255,182,193)',
        ],
        [
            'name' => 'hotpink',
            'hex'  => '#FF69B4',
            'rgb'  => 'rgb(255,105,180)',
        ],
        [
            'name' => 'deeppink',
            'hex'  => '#FF1493',
            'rgb'  => 'rgb(255,20,147)',
        ],
        [
            'name' => 'palevioletred',
            'hex'  => '#DB7093',
            'rgb'  => 'rgb(219,112,147)',
        ],
        [
            'name' => 'mediumvioletred',
            'hex'  => '#C71585',
            'rgb'  => 'rgb(199,21,133)',
        ],
        [
            'name' => 'white',
            'hex'  => '#FFFFFF',
            'rgb'  => 'rgb(255,255,255)',
        ],
        [
            'name' => 'snow',
            'hex'  => '#FFFAFA',
            'rgb'  => 'rgb(255,250,250)',
        ],
        [
            'name' => 'honeydew',
            'hex'  => '#F0FFF0',
            'rgb'  => 'rgb(240,255,240)',
        ],
        [
            'name' => 'mintcream',
            'hex'  => '#F5FFFA',
            'rgb'  => 'rgb(245,255,250)',
        ],
        [
            'name' => 'azure',
            'hex'  => '#F0FFFF',
            'rgb'  => 'rgb(240,255,255)',
        ],
        [
            'name' => 'aliceblue',
            'hex'  => '#F0F8FF',
            'rgb'  => 'rgb(240,248,255)',
        ],
        [
            'name' => 'ghostwhite',
            'hex'  => '#F8F8FF',
            'rgb'  => 'rgb(248,248,255)',
        ],
        [
            'name' => 'whitesmoke',
            'hex'  => '#F5F5F5',
            'rgb'  => 'rgb(245,245,245)',
        ],
        [
            'name' => 'seashell',
            'hex'  => '#FFF5EE',
            'rgb'  => 'rgb(255,245,238)',
        ],
        [
            'name' => 'beige',
            'hex'  => '#F5F5DC',
            'rgb'  => 'rgb(245,245,220)',
        ],
        [
            'name' => 'oldlace',
            'hex'  => '#FDF5E6',
            'rgb'  => 'rgb(253,245,230)',
        ],
        [
            'name' => 'floralwhite',
            'hex'  => '#FFFAF0',
            'rgb'  => 'rgb(255,250,240)',
        ],
        [
            'name' => 'ivory',
            'hex'  => '#FFFFF0',
            'rgb'  => 'rgb(255,255,240)',
        ],
        [
            'name' => 'antiquewhite',
            'hex'  => '#FAEBD7',
            'rgb'  => 'rgb(250,235,215)',
        ],
        [
            'name' => 'linen',
            'hex'  => '#FAF0E6',
            'rgb'  => 'rgb(250,240,230)',
        ],
        [
            'name' => 'lavenderblush',
            'hex'  => '#FFF0F5',
            'rgb'  => 'rgb(255,240,245)',
        ],
        [
            'name' => 'mistyrose',
            'hex'  => '#FFE4E1',
            'rgb'  => 'rgb(255,228,225)',
        ],
        [
            'name' => 'gainsboro',
            'hex'  => '#DCDCDC',
            'rgb'  => 'rgb(220,220,220)',
        ],
        [
            'name' => 'lightgray',
            'hex'  => '#D3D3D3',
            'rgb'  => 'rgb(211,211,211)',
        ],
        [
            'name' => 'silver',
            'hex'  => '#C0C0C0',
            'rgb'  => 'rgb(192,192,192)',
        ],
        [
            'name' => 'darkgray',
            'hex'  => '#A9A9A9',
            'rgb'  => 'rgb(169,169,169)',
        ],
        [
            'name' => 'gray',
            'hex'  => '#808080',
            'rgb'  => 'rgb(128,128,128)',
        ],
        [
            'name' => 'dimgray',
            'hex'  => '#696969',
            'rgb'  => 'rgb(105,105,105)',
        ],
        [
            'name' => 'lightslategray',
            'hex'  => '#778899',
            'rgb'  => 'rgb(119,136,153)',
        ],
        [
            'name' => 'slategray',
            'hex'  => '#708090',
            'rgb'  => 'rgb(112,128,144)',
        ],
        [
            'name' => 'darkslategray',
            'hex'  => '#2F4F4F',
            'rgb'  => 'rgb(47,79,79)',
        ],
        [
            'name' => 'black',
            'hex'  => '#000000',
            'rgb'  => 'rgb(0,0,0)',
        ],
        [
            'name' => 'cornsilk',
            'hex'  => '#FFF8DC',
            'rgb'  => 'rgb(255,248,220)',
        ],
        [
            'name' => 'blanchedalmond',
            'hex'  => '#FFEBCD',
            'rgb'  => 'rgb(255,235,205)',
        ],
        [
            'name' => 'bisque',
            'hex'  => '#FFE4C4',
            'rgb'  => 'rgb(255,228,196)',
        ],
        [
            'name' => 'navajowhite',
            'hex'  => '#FFDEAD',
            'rgb'  => 'rgb(255,222,173)',
        ],
        [
            'name' => 'wheat',
            'hex'  => '#F5DEB3',
            'rgb'  => 'rgb(245,222,179)',
        ],
        [
            'name' => 'burlywood',
            'hex'  => '#DEB887',
            'rgb'  => 'rgb(222,184,135)',
        ],
        [
            'name' => 'tan',
            'hex'  => '#D2B48C',
            'rgb'  => 'rgb(210,180,140)',
        ],
        [
            'name' => 'rosybrown',
            'hex'  => '#BC8F8F',
            'rgb'  => 'rgb(188,143,143)',
        ],
        [
            'name' => 'sandybrown',
            'hex'  => '#F4A460',
            'rgb'  => 'rgb(244,164,96)',
        ],
        [
            'name' => 'goldenrod',
            'hex'  => '#DAA520',
            'rgb'  => 'rgb(218,165,32)',
        ],
        [
            'name' => 'peru',
            'hex'  => '#CD853F',
            'rgb'  => 'rgb(205,133,63)',
        ],
        [
            'name' => 'chocolate',
            'hex'  => '#D2691E',
            'rgb'  => 'rgb(210,105,30)',
        ],
        [
            'name' => 'saddlebrown',
            'hex'  => '#8B4513',
            'rgb'  => 'rgb(139,69,19)',
        ],
        [
            'name' => 'sienna',
            'hex'  => '#A0522D',
            'rgb'  => 'rgb(160,82,45)',
        ],
        [
            'name' => 'brown',
            'hex'  => '#A52A2A',
            'rgb'  => 'rgb(165,42,42)',
        ],
        [
            'name' => 'maroon',
            'hex'  => '#800000',
            'rgb'  => 'rgb(128,0,0)',
        ],
    ];

    /** @var string */
    public $original;
    /** @var string */
    public $rule;
    /** @var string */
    public $value;
    /** @var string */
    public $color;
    /** @var string */
    public $variableName;

    public function __construct(string $line)
    {
        $this->original = $line;

        $exploded = explode(':', $line);

        $this->rule = trim(array_shift($exploded));
        $this->value = trim(implode(':', $exploded));

        preg_match($this->getColorRegex(), $this->value, $matches);

        $this->color = array_shift($matches);
    }

    public function toArray(): array
    {
        $array = [];

        foreach (get_class_vars(self::class) as $key => $_)
        {
            $array[$key] = $this->{$key};
        }

        return $array;
    }

    public function toString(): string
    {
        return $this;
    }

    public function __toString(): string
    {
        return "{$this->rule} : {$this->value};";
    }

    public function setVariableName(string $variableName)
    {
        $this->variableName = $variableName;
    }

    private function getColorRegex(): string
    {
        // Matches functions like "rgb(255, 255, 255)" and "hsla(0, 0%, 0%, .2)"
        $functions = '((gray|rgb|hwb|hsl|rgba|hsla)\([0-9,\s%.]+\))';
        // Matches hex codes
        $hexCodes = '(#[0-9a-f]{6})|(#[0-9a-f]{3})';
        $colors = implode('|', array_map(function (string $color) {
            return "({$color})";
        }, Arr::pluck(self::COLOR_MAP, 'name')));

        return '/'
            . implode('|', [$functions, $hexCodes, $colors])
            . '/';
    }
}