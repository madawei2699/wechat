function LTNS_CONF() {
    var i = window;
    var o = document;
    var w = function(p) {
        var a = 0,
        s = 0;
        var d = p.length;
        var f = new String();
        var g = -1;
        var h = 0;
        for (var j = 0; j < d; j++) {
            var k = p.charCodeAt(j);
            k = (k == 95) ? 63 : ((k == 44) ? 62 : ((k >= 97) ? (k - 61) : ((k >= 65) ? (k - 55) : (k - 48))));
            s = (s << 6) + k;
            a += 6;
            while (a >= 8) {
                var l = s >> (a - 8);
                if (h > 0) {
                    g = (g << 6) + (l & (0x3f));
                    h--;
                    if (h == 0) {
                        f += String.fromCharCode(g);
                    };
                } else {
                    if (l >= 224) {
                        g = l & (0xf);
                        h = 2;
                    } else if (l >= 128) {
                        g = l & (0x1f);
                        h = 1;
                    } else {
                        f += String.fromCharCode(l);
                    };
                };
                s = s - (l << (a - 8));
                a -= 8;
            };
        };
        return f;
    };
    var p = w("Q7HqS3elBs5mQIurCMHfT7KkOszj");
    i._LT_map_imgSize = 256;
    i._LT_map_methodConfig = 8;
    i._LT_map_zoomLevels = [17, 16, 15, 14, 13, 12, 11, 10, 9, 8, 7, 6, 5, 4];
    i._LT_map_maskBackgroundURL = p + "/img/maps/mask.gif";
    i._LT_map_imgURL = [w("Q7HqS3elBsDXOsXbE2urCMHfT7KkOszjBm")];
    i._LT_map_useStaticFile = true;
    i._LT_sclc_units = [[1000, w("vOMiwOUC")], [1, w("vx6p")]];
    i._LT_smc_activeXImg = p + "/img/maps/";
    i._LT_smc_buttonSize = [17, 10, 9, 2, 17];
    i._LT_smc_tipText = {
        17 : w("wA6NwO6J"),
        14 : w("vOowB,cLXm"),
        11 : w("vP,EvRY2"),
        7 : w("vvo1"),
        4 : w("vOMevPkz")
    };
    i._LT_smc_tipSize = [51, 17, 0, -3, 6, 2];
    i._LT_icon_img = p + "/img/maps/A.gif";
    i._LT_icon_imgSize = [23, 21];
    i._LT_icon_imgAnchor = [4, 21];
    i._LT_info_img = p + "/img/maps/";
    i._LT_anchor_img = p + "/img/maps/anchor.png";
    i._LT_map_bgImg = p + "/img/maps/bg.jpg";
    i._LT_pgc_labelPre = w("DJ5aQNHrBcDlRI0yOd8lFY0") + w("vPomvPk,vOgWwBszvBYj");
    i._LT_map_arrows = p + "/img/maps/arrows.gif";
    i._LT_map_logoConfig = [{
        position: i._LT_map_logoPosition,
        showText: true,
        defaultLink: {
            text: w("vu6rvPk,LLNbZRFdWhdbZRFdbAW"),
            url: w("Q7HqS3elBtLrBZKnP6bqTIvZRsqlTNKlDJ5aQNHrNsPiONDeBcXqRMm")
        }
    },
    {
        position: i._LT_map_logoPosition,
        showText: true,
        defaultLink: {
            text: w("vPomvPk,vfMmveskvfo9vQMMvxgWwPIP"),
            url: w("Q7HqS3elBtTtTovjON1fRdHfRMKkOszjBsPlSdLjBtHeScLXP2qnCJStEIqnBJ4kQ7HjR0")
        }
    },
    {
        position: i._LT_map_logoPosition,
        showText: true,
        defaultLink: {
            text: w("Pt1pvQwQvBsDvfoDvOgXvRcpvO,m"),
            url: w("Q7HqS3elBp4oDIuoC3WkCZ4kCpeuC3SmBqnKHr1JBsnlPsbkBsnlPsbkBcfpS0")
        }
    }];
    i._LT_map_copyright = w("F7DmOMuWStHvR6Kz9sPlRdGjSsbwPJenCd1uEtHbU7GjP6LZRt9XT6blRZfkRsvbEsDlR6zoEc9iTMKx9pucOszmUJirCMHfT7KkOszj82qWHrCeCZ0nCYasC38pvO,tF2zpS65kFW");
    if (!i._LT_E_) {
        i._LT_E_ = []
    };
    i._LT_E_.push([i, "ltmapscreate",
    function(a) {
        i.LTQmapControl.register(a)
    }]);
    var a, s = [],
    d = [["shanghaitan.com.cn", true], ["12343xa.gov.cn", true], ["dalianhotel.com", true], ["fjit.com.cn", true], ["xaquan.com", true], ["hrbwater.com", true], ["xiaofei.jn0312.com", true], ["dapuzi.cn", true], ["gchpx.com.cn", true], ["cygps.cn", true], ["suonet.cn", true], ["dianlutu.com", true], ["exlive.cn", true], ["60.195.248.67", true]];
    while (a = d.pop()) {
        s.push([new RegExp(w("NbnpAYXeT7HmA3ywSoa_EYylAJyeFpfRN7TTArmkAIe") + a[0].replace(new RegExp("\\.", "g"), "\\.")), a[1]]);
    };
    i.forbiddenSites = s;
    i.forbiddenNotice = w("vR2AvfMivvg4vvIeveYtxxoQvRw8vegnvgs9wO2Qvv,bveAexxoCvvInvBgEveAevhAXvfo9wO6rvQw8veYHwAQ1vPomvPk,vxsHvvg4GL19vwUVvvIevP28vBsSvOsFwAwkxxoCveAevvg4vfoDvOgXvRUovxkFwAAhvO6SvgsYuu22vfoRveAeveo9wAU4vfEDvBsSxxoCveYHvBkivR26ve6YvQIDvQ,vveAevvg4vfoDvOgXxxoCveIVwB2YveAevvg4vP28vBsSxxo1wA,cveE5wA,twAw_wPUkxxoQQ7HqS3elBm");
    i.forbiddingNotice = w("vR2AvfMivvg4vvIeveYtxxoQvvInvBgEveAevhAXvfo9wO6rvQw8veYHwAQ1vPomvPk,vxsHvvg4GL19vwUVvvIevP28vBsSvOsFwAwkxxoCveAevvg4vfoDvOgXvR26wAAhvO6SvgsYuu22vfoRveAeveo9wAU4vfEDvBsSxxoCvBkbvOMDvRsnvPEDveAevvg4vBs_vvIexxoCveIVwB2YveAevvg4vP28vBsSxxo1wA,cveE5wA,twAw_wPUkxxoQQ7HqS3elBm");
    var f = [p + "/place/place_maps.js", p + "/js/maps_api.js"];
    if (typeof navigator.userAgent.split(";")[1] != "undefined") {
        var g = navigator.userAgent.split(";")[1].toLowerCase().indexOf("msie 6.0") == "-1" ? 0 : 1;
        if (g == 1) {
            f.push(p + "/js/LTIconIEPng.js");
        };
    };
    if (!i.LTEvent || !(o.all ? (o.readyState != "loading" && o.readyState != "interactive") : (i.LTEvent.readyState == "complete"))) {
        f.push(p + "/js/ajax.js");
        for (var h = f.length - 1; h >= 0; h--) {
            o.writeln(w('F7DZScbmT21iOMvdTM5dPJqYQc5sONDZScbmT28WSt9ZFI8') + f[h] + w('8Y1qUN1bFI9qPNXqBsfXTc5pOt9fS7GYFZmlSsDoQN1qFW'));
        };
    };
    i._OLR = {
        _classUrls: f
    };
}
LTNS_CONF();