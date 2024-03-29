// コマンドライン引数などを取得するためのもの
// (node_modulesに含まれていないライブラリだが、node自体に組み込まれているので使える)
const process = require("process");

// node_modules/akrantiain 内に入っているライブラリを使う
const {Akrantiain} = require("akrantiain");

// 1つめのコマンドライン引数を取得する ([0]は"node", [1]は"convert-pronunciation.js" になっているので、 [2]を取得する)
let input = process.argv[2];

let akrantiain = Akrantiain.load(`
@FALL_THROUGH
#@CASE_SENSITIVE
V = "e"|"a"|"o"|"i"|"u";
Vcap = "E"|"A"|"O"|"I"|"U";
C = "h"|"k"|"s"|"t"|"c"|"n"|"r"|"m"|"p"|"f"|"g"|"z"|"d"|"b"|"v"|"s'"|"t'"|"n'"|"r'"|"z'"|"d'";
Ccap = "H"|"K"|"S"|"T"|"C"|"N"|"R"|"M"|"P"|"F"|"G"|"Z"|"D"|"B"|"V"|"S'"|"T'"|"N'"|"R'"|"Z'"|"D'";
Sym = "(" | ")" | "-";
Cs = ""|C|C C|Sym|Sym C|Sym C C;
Cscap = ""|Ccap|Ccap Ccap|Sym|Sym Ccap|Sym Ccap Ccap;
S = Cs V Cs;
Scap = Cscap Vcap Cscap;

# 前舌母音
Vf = "i"|"e"|"a";
# 後舌母音
Vr = "u"|"o";
# 狭母音
Vc = "i"|"u";
# 中央母音
Vm = "e"|"o";
# 広母音
Vo = "a";

# 唇音
Cl = "p"|"b"|"m"|"f"|"v";
# 歯茎音
Ca = "t"|"d"|"c"|"n"|"s"|"z"|"r'"|"r";
# 硬口蓋音
Cpv = "t'"|"d'"|"n'"|"s'"|"z'";
# 軟口蓋音
Cv = "k"|"g"|"h";

# 破裂音
Cp = "p"|"b"|"t"|"d"|"k"|"g";
# 破擦音
Cn = "c"|"t'"|"d'";
# 鼻音
Ctr = "m"|"n"|"n'";
# 摩擦音
Cfr = "f"|"v"|"s"|"z"|"s'"|"z'"|"h";
# ふるえ音
Clf = "r'";
# はじき音
Cap = "r";

Cvoiced = "g"|"z"|"d"|"b"|"v"|"z'"|"d'";
Cunvoiced = "h"|"k"|"s"|"t"|"c"|"p"|"f"|"s'"|"t'";

#母音の特殊発音
"e" "e" Cs ^ -> $ /we/ $;
#"e" "e" Cs S ^ -> $ /wɜː/ $ $;
"e" "e" Cs S ^ -> $ /wE/ $ $;
"e" "e" Cs "-" -> $ /we/ $ $;
#"e" "e" Cs S "-" -> $ /wɜː/ $ $ $;
"e" "e" Cs S "-" -> $ /wE/ $ $ $;
"i" "" "i" -> $ /w/ $;
"i" "" V -> $ /y/ $;
"u" "" V -> $ /w/ $;
"a" "" "a" -> $ /w/ $;
"o" "" ("o"|"a") -> $ /w/ $;

#長母音
"e""e" -> $ /ː/;

#アクセント
#"k" V Cs S^ -> /kʰ/ $ $ $;
#"t'" V Cs S^ -> /t͡ʃʰ/ $ $ $;
#"t" V Cs S^ -> /tʰ/ $ $ $;
#"c" V Cs S^ -> /t͡sʰ/ $ $ $;
#"p" V Cs S^ -> /pʰ/ $ $ $;
#
#"k" V Cs S "-" -> /kʰ/ $ $ $ $;
#"t'" V Cs S "-" -> /t͡ʃʰ/ $ $ $ $;
#"t" V Cs S "-" -> /tʰ/ $ $ $ $;
#"c" V Cs S "-" -> /t͡sʰ/ $ $ $ $;
#"p" V Cs S "-" -> /pʰ/ $ $ $ $;

^ Cs "e" Cs^ -> $ /ɜː/ $;
^ Cs "a" Cs^ -> $ /aː/ $;
^ Cs "o" Cs^ -> $ /ɔː/ $;
^ Cs "i" Cs^ -> $ /iː/ $;
^ Cs "u" Cs^ -> $ /uː/ $;

"e" Cs S^ -> /ɜː/ $ $;
"a" Cs S^ -> /aː/ $ $;
"o" Cs S^ -> /ɔː/ $ $;
"i" Cs S^ -> /iː/ $ $;
"u" Cs S^ -> /uː/ $ $;

"e" Cs S "-" -> /ɜː/ $ $ $;
"a" Cs S "-" -> /aː/ $ $ $;
"o" Cs S "-" -> /ɔː/ $ $ $;
"i" Cs S "-" -> /iː/ $ $ $;
"u" Cs S "-" -> /uː/ $ $ $;

# 唇音+唇音は、後続子音の長子音になる。ただし、m+破裂音を除く。
"m" ("p"|"b") -> /m/ $;
Cl "p" -> /p/ $;
Cl "b" -> /b/ $;
Cl "m" -> /m/ $;
Cl "f" -> /f/ $;
Cl "v" -> /v/ $;

# 歯茎音+歯茎音は、破裂音・破擦音間は後続子音の有声・無声に合わせて前置子音の有声・無声が変化する。
"d" "t" -> /t/ $;
"t" "d" -> /d/ $;
"d" "c" -> /t/ $;

# /ts/, /dz/ はそれぞれ c, d' 相当の音に変化する。
"t" "s" -> // /t͡s/;
"d" "z" ->// /d͡ʒ/;

# 硬口蓋音+硬口蓋音は、破裂音間では後続子音の長子音になる。
"k" "g"->/g/ $;
"g" "k"->/k/ $;

# 通常の子音のうち、そのままではまずいもの
"h" -> /x/;
"s'" -> /ʃ/;
"t'" -> /t͡ʃ/;
"c" -> /t͡s/;
"n'" -> /ɲ/;
"r'" -> /r/;
"r" -> /l/;
"z'" -> /ʒ/;
"d'" -> /d͡ʒ/;
    `);
let output = akrantiain.convert(input);

// 変換結果を出力する
console.log(output);
