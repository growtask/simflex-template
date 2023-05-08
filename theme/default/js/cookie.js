function JSCookie() {

    /**
     * 
     * @param {string} c_name
     * @returns {String}
     */
    this.get = function (c_name) {
        var i, x, y, ARRcookies = document.cookie.split(";");
        for (i = 0; i < ARRcookies.length; i++)
        {
            x = ARRcookies[i].substr(0, ARRcookies[i].indexOf("="));
            y = ARRcookies[i].substr(ARRcookies[i].indexOf("=") + 1);
            x = x.replace(/^\s+|\s+$/g, "");
            if (x == c_name)
            {
                return unescape(y);
            }
        }
        return '';
    }

    /**
     * 
     * @param {string} name
     * @param {string} value
     * @param {int} expires in seconds
     * @param {string} path
     * @param {string} domain
     * @param {bool} secure
     * @returns {Boolean|Number}
     */
    this.set = function (name, value, expires, path, domain, secure) {	// Send a cookie
        expires instanceof Date ? expires = expires.toGMTString() : typeof (expires) == 'number' && (expires = (new Date(+(new Date) + expires * 1e3)).toGMTString());
        var r = [name + "=" + escape(value)], s, i;
        for (i in s = {expires: expires, path: path, domain: domain}) {
            s[i] && r.push(i + "=" + s[i]);
        }
        return secure && r.push("secure"), document.cookie = r.join(";"), true;
    }
}
var Cookie = new JSCookie();