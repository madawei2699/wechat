var _LT_map_Rego = "http://rgc.vip.51ditu.com/rgc?pos=";
function LTNS() {
    LTNS.info = {
        time: 'Thu Jul 19 10:01:06 UTC+0800 2012',
        version: '0.01',
        ov: '1.3 Ver 20070705'
    };
    var w = function(g) {
        var h = 0,
        j = 0;
        var k = g.length;
        var l = new String();
        var z = -1;
        var x = 0;
        for (var c = 0; c < k; c++) {
            var v = g.charCodeAt(c);
            v = (v == 95) ? 63 : ((v == 44) ? 62 : ((v >= 97) ? (v - 61) : ((v >= 65) ? (v - 55) : (v - 48))));
            j = (j << 6) + v;
            h += 6;
            while (h >= 8) {
                var b = j >> (h - 8);
                if (x > 0) {
                    z = (z << 6) + (b & (0x3f));
                    x--;
                    if (x == 0) {
                        l += String.fromCharCode(z);
                    };
                } else {
                    if (b >= 224) {
                        z = b & (0xf);
                        x = 2;
                    } else if (b >= 128) {
                        z = b & (0x1f);
                        x = 1;
                    } else {
                        l += String.fromCharCode(b);
                    };
                };
                j = j - (b << (h - 8));
                h -= 8;
            };
        };
        return l;
    };
    var q = ["/ReverseGeocode/Area", "GB2312", ",", "error", "loaded"];
    var i = window;
    var o = document;
    function f(g, h) {
        for (var j in h) {
            g[j] = h[j];
        };
    }
    function a(f) {
        var e = this;
        e.win = f ? f: i;
        e.loader = new LTObjectLoader(e.win);
        e.url = i._LT_map_Rego ? i._LT_map_Rego: w("Q7HqS3elBt9dOovsQN0kDJ5aQNHrBcDlRIzoPsC_S6zpFG");
        LTEvent.bind(e.loader, q[4], e, e.onLoad);
        LTEvent.bind(e.loader, q[3], e, e.onError);
    };
    f(a.prototype, {
        loadRego: function(f) {
            var e = this;
            var g = e.url + f[0] + q[2] + f[1] + "&type=0";
            LTAjax.loadRemoteXml(g, LTEvent.createAdapter(e, e.onLoad), q[1]);
        },
        loadDescribe: function(f) {
            var e = this;
            var g = e.url + f[0] + q[2] + f[1] + "&type=1";
            LTAjax.loadRemoteXml(g, LTEvent.createAdapter(e, e.onLoad), q[1]);
        },
        onLoad: function(f) {
            var e = this;
            var g = {};
            if (LTAjax.selectNodes(f, q[0])[0]) {
                g.rego = LTAjax.getNodeValue(LTAjax.selectNodes(f, "/ReverseGeocode/Area/@regionCode")[0]);
                g.t = LTAjax.getNodeValue(LTAjax.selectNodes(f, q[0])[0]);
                LTEvent.trigger(e, q[4], [g]);
            } else {
                g.describe = LTAjax.getNodeValue(LTAjax.selectNodes(f, "/R/msg")[0]);
                LTEvent.trigger(e, q[4], [g]);
            };
        },
        onError: function() {
            var e = this;
            LTEvent.trigger(e, q[3], []);
        }
    });
    var p = function(a) {
        var s = o.getElementsByTagName(w("SsDoQN1q"));
        var d = new RegExp(a, "i");
        for (var f = 0; f < s.length; f++) {
            var g = s[f];
            if (g.src && d.test(g.src)) {
                break;
            };
        };
        return ! o.all || f < s.length;
    };
    if (!p(w("NbnpAYXeT7HmA3ywSoa_EYylAJyeFpfRN7TTArmkAIerCMHfT7LSBcDlRIzgSozoPMTlN2vgSrnpAYG"))) return false;
    f(i, {
        LTRegoLoader: a
    })
};
LTNS();