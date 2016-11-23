var itv_m3u8PlayerUrl = "http://epg.test.itv.cn/player/test/VideoPlayer2.swf?version=61_test";
var itv_playerSkinUrl = "http://player.test.itv.cn/skin/Default.swf";
//var itv_logUrl = "http://bilog.test.itv.cn/Online.ashx";
//var itv_statusUrl = "http://bilog.test.itv.cn/OnlineStatus.ashx";
var itv_globalWatermarkUrl = "";

var itv_videoplayer_local_zh_cn = "缓冲中，请稍候。。。|跳转|音量";
var itv_videoplayer_local_en_us = "loading...|seek|volume";
var itv_videoplayer_local_th_th = "loading...|seek|volume";

if (typeof deconcept == "undefined") { var deconcept = new Object(); } if (typeof deconcept.util == "undefined") { deconcept.util = new Object(); } if (typeof deconcept.SWFObjectUtil == "undefined") { deconcept.SWFObjectUtil = new Object(); } deconcept.SWFObject = function (_1, id, w, h, _5, c, _7, _8, _9, _a) { if (!document.getElementById) { return; } this.DETECT_KEY = _a ? _a : "detectflash"; this.skipDetect = deconcept.util.getRequestParameter(this.DETECT_KEY); this.params = new Object(); this.variables = new Object(); this.attributes = new Array(); if (_1) { this.setAttribute("swf", _1); } if (id) { this.setAttribute("id", id); } if (w) { this.setAttribute("width", w); } if (h) { this.setAttribute("height", h); } if (_5) { this.setAttribute("version", new deconcept.PlayerVersion(_5.toString().split("."))); } this.installedVer = deconcept.SWFObjectUtil.getPlayerVersion(); if (!window.opera && document.all && this.installedVer.major > 7) { deconcept.SWFObject.doPrepUnload = true; } if (c) { this.addParam("bgcolor", c); } var q = _7 ? _7 : "high"; this.addParam("quality", q); this.setAttribute("useExpressInstall", false); this.setAttribute("doExpressInstall", false); var _c = (_8) ? _8 : window.location; this.setAttribute("xiRedirectUrl", _c); this.setAttribute("redirectUrl", ""); if (_9) { this.setAttribute("redirectUrl", _9); } }; deconcept.SWFObject.prototype = { useExpressInstall: function (_d) { this.xiSWFPath = !_d ? "expressinstall.swf" : _d; this.setAttribute("useExpressInstall", true); }, setAttribute: function (_e, _f) { this.attributes[_e] = _f; }, getAttribute: function (_10) { return this.attributes[_10]; }, addParam: function (_11, _12) { this.params[_11] = _12; }, getParams: function () { return this.params; }, addVariable: function (_13, _14) { this.variables[_13] = _14; }, getVariable: function (_15) { return this.variables[_15]; }, getVariables: function () { return this.variables; }, getVariablePairs: function () { var _16 = new Array(); var key; var _18 = this.getVariables(); for (key in _18) { _16[_16.length] = key + "=" + _18[key]; } return _16; }, getSWFHTML: function () { var _19 = ""; if (navigator.plugins && navigator.mimeTypes && navigator.mimeTypes.length) { if (this.getAttribute("doExpressInstall")) { this.addVariable("MMplayerType", "PlugIn"); this.setAttribute("swf", this.xiSWFPath); } _19 = "<embed type=\"application/x-shockwave-flash\" src=\"" + this.getAttribute("swf") + "\" width=\"" + this.getAttribute("width") + "\" height=\"" + this.getAttribute("height") + "\" style=\"" + this.getAttribute("style") + "\""; _19 += " id=\"" + this.getAttribute("id") + "\" name=\"" + this.getAttribute("id") + "\" "; var _1a = this.getParams(); for (var key in _1a) { _19 += [key] + "=\"" + _1a[key] + "\" "; } var _1c = this.getVariablePairs().join("&"); if (_1c.length > 0) { _19 += "flashvars=\"" + _1c + "\""; } _19 += "/>"; } else { if (this.getAttribute("doExpressInstall")) { this.addVariable("MMplayerType", "ActiveX"); this.setAttribute("swf", this.xiSWFPath); } _19 = "<object id=\"" + this.getAttribute("id") + "\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" width=\"" + this.getAttribute("width") + "\" height=\"" + this.getAttribute("height") + "\" style=\"" + this.getAttribute("style") + "\">"; _19 += "<param name=\"movie\" value=\"" + this.getAttribute("swf") + "\" />"; var _1d = this.getParams(); for (var key in _1d) { _19 += "<param name=\"" + key + "\" value=\"" + _1d[key] + "\" />"; } var _1f = this.getVariablePairs().join("&"); if (_1f.length > 0) { _19 += "<param name=\"flashvars\" value=\"" + _1f + "\" />"; } _19 += "</object>"; } return _19; }, write: function (_20) { if (this.getAttribute("useExpressInstall")) { var _21 = new deconcept.PlayerVersion([6, 0, 65]); if (this.installedVer.versionIsValid(_21) && !this.installedVer.versionIsValid(this.getAttribute("version"))) { this.setAttribute("doExpressInstall", true); this.addVariable("MMredirectURL", escape(this.getAttribute("xiRedirectUrl"))); document.title = document.title.slice(0, 47) + " - Flash Player Installation"; this.addVariable("MMdoctitle", document.title); } } if (this.skipDetect || this.getAttribute("doExpressInstall") || this.installedVer.versionIsValid(this.getAttribute("version"))) { var n = (typeof _20 == "string") ? document.getElementById(_20) : _20; n.innerHTML = this.getSWFHTML(); return true; } else { if (this.getAttribute("redirectUrl") != "") { document.location.replace(this.getAttribute("redirectUrl")); } } return false; } }; deconcept.SWFObjectUtil.getPlayerVersion = function () { var _23 = new deconcept.PlayerVersion([0, 0, 0]); if (navigator.plugins && navigator.mimeTypes.length) { var x = navigator.plugins["Shockwave Flash"]; if (x && x.description) { _23 = new deconcept.PlayerVersion(x.description.replace(/([a-zA-Z]|\s)+/, "").replace(/(\s+r|\s+b[0-9]+)/, ".").split(".")); } } else { if (navigator.userAgent && navigator.userAgent.indexOf("Windows CE") >= 0) { var axo = 1; var _26 = 3; while (axo) { try { _26++; axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash." + _26); _23 = new deconcept.PlayerVersion([_26, 0, 0]); } catch (e) { axo = null; } } } else { try { var axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.7"); } catch (e) { try { var axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash.6"); _23 = new deconcept.PlayerVersion([6, 0, 21]); axo.AllowScriptAccess = "always"; } catch (e) { if (_23.major == 6) { return _23; } } try { axo = new ActiveXObject("ShockwaveFlash.ShockwaveFlash"); } catch (e) { } } if (axo != null) { _23 = new deconcept.PlayerVersion(axo.GetVariable("$version").split(" ")[1].split(",")); } } } return _23; }; deconcept.PlayerVersion = function (_29) { this.major = _29[0] != null ? parseInt(_29[0]) : 0; this.minor = _29[1] != null ? parseInt(_29[1]) : 0; this.rev = _29[2] != null ? parseInt(_29[2]) : 0; }; deconcept.PlayerVersion.prototype.versionIsValid = function (fv) { if (this.major < fv.major) { return false; } if (this.major > fv.major) { return true; } if (this.minor < fv.minor) { return false; } if (this.minor > fv.minor) { return true; } if (this.rev < fv.rev) { return false; } return true; }; deconcept.util = { getRequestParameter: function (_2b) { var q = document.location.search || document.location.hash; if (_2b == null) { return q; } if (q) { var _2d = q.substring(1).split("&"); for (var i = 0; i < _2d.length; i++) { if (_2d[i].substring(0, _2d[i].indexOf("=")) == _2b) { return _2d[i].substring((_2d[i].indexOf("=") + 1)); } } } return ""; } }; deconcept.SWFObjectUtil.cleanupSWFs = function () { var _2f = document.getElementsByTagName("OBJECT"); for (var i = _2f.length - 1; i >= 0; i--) { _2f[i].style.display = "none"; for (var x in _2f[i]) { if (typeof _2f[i][x] == "function") { _2f[i][x] = function () { }; } } } }; if (deconcept.SWFObject.doPrepUnload) { if (!deconcept.unloadSet) { deconcept.SWFObjectUtil.prepUnload = function () { __flash_unloadHandler = function () { }; __flash_savedUnloadHandler = function () { }; window.attachEvent("onunload", deconcept.SWFObjectUtil.cleanupSWFs); }; window.attachEvent("onbeforeunload", deconcept.SWFObjectUtil.prepUnload); deconcept.unloadSet = true; } } if (!document.getElementById && document.all) { document.getElementById = function (id) { return document.all[id]; }; } var getQueryParamValue = deconcept.util.getRequestParameter; var FlashObject = deconcept.SWFObject; var SWFObject = deconcept.SWFObject;

var itv_videoPlayerObject;
function VideoPlayer(containerId, serverType, contentType, languageType, params) {
    this._containerId = containerId;
    this._serverType = serverType;
    this._contentType = contentType;
    this._languageType = languageType;

    this._playerId = containerId + "_flashVideoPlayer";
    this._platformType = this.PlatformType.pc;
    this._prevDuration = 0;
    this._prevPlayedTime = 0;
    this._prevPausedTime = 0;
    this._prevBufferTime = 0;
    this._bufferCount = 0;
    this._bufferDurationTotal = 0;
    this._bufferDurationFirst = 0;
    this._downloadSpeedTotal = 0;
    this._downloadCount = 0;
    this._currentTimeOffset = 0; // 服务器时间与本地时间的偏移差
    this._key = ""; // 保证一次播放唯一
    this._elementType = VideoPlayer.ElementType.none;
    this._elementId = "";

    if (navigator.userAgent.indexOf("iPad") > -1)
        this._deviceType = this.DeviceType.iPad;
    else if (navigator.userAgent.indexOf("iPhone") > -1)
        this._deviceType = this.DeviceType.iPhone;
    else if (navigator.userAgent.toLowerCase().indexOf("android") > -1)
        this._deviceType = this.DeviceType.aPad;
    else
        this._deviceType = this.DeviceType.pc;

    this.programId = "";
    this.userToken = "";
    this.seriesParentId = "";
    this.seriesNumber = 0;
    this.contentBM = 0; // bookmark 书签
    this.channelNumber = "";
    this.startTime = "";
    this.endTime = "";

    this.maxBufferLength = 35;
    if (typeof params != 'undefined')
        this.maxBufferLength = params.maxBufferLength;

    this.init();

    itv_videoPlayerObject = this;
}

VideoPlayer.prototype = {
    play: function (playUrl, elementType, elementId) {
        this._key = Math.random().toString().replace(".", "");
        var container = document.getElementById(this._containerId);

        if (this._deviceType == this.DeviceType.iPad || this._deviceType == this.DeviceType.iPhone) {
            container.innerHTML = "<div id=\"videoParent_" + this._playerId + "\" style='position:relative'><video id=\"" + this._playerId + "\" width=\"100%\" height=\"100%\" preload=\"metadata\" controls=\"controls\"><source /></video></div>";
            var player = document.getElementById(this._playerId);

            player.src = playUrl;

            player.load();
            player.play();

            player.addEventListener("pause", itv_videoPlayPaused, false);
            player.addEventListener("playing", itv_videoPlayPlayed, false);
            player.addEventListener("waiting", itv_videoPlayBuffer, false);
            player.addEventListener("ended", itv_videoPlayCompleted, false);
            player.addEventListener("timeupdate", itv_videoCurrentTime, false);
        }
        else {
            var player = document.getElementById(this._playerId);

            if (this._contentType == VideoPlayer.ContentType.live || this._contentType == VideoPlayer.ContentType.virtual) {
                player.vSetStartTime(this.getTimestamp(this.startTime));
                player.vSetEndTime(this.getTimestamp(this.endTime));
            }

            if (typeof elementType == 'undefined')
                player.vGotoAndPlay(this.programId, playUrl, this.contentBM, VideoPlayer.ElementType.none);
            else
                player.vGotoAndPlay(this.programId, playUrl, this.contentBM, elementType);

            //var myContext = this;
            //VideoPlayer.timerLogStatus = setTimeout(function () { myContext.callLogStatus.call(myContext); }, 5 * 60 * 1000);
        }

        this._prevPlayedTime = new Date().getTime();
        this._prevDuration = 0;
        //this.callLog();
        //this.callLog2();
        //this.callLog3(elementType, elementId);
    },

    addWatermarkHtml5: function () {
        if (typeof itv_globalWatermark != "undefined" && itv_globalWatermark != "") {
            var videoParentDiv = document.getElementById("videoParent_" + this._playerId);
            videoParentDiv.innerHTML += "<div style='position:absolute; top:5px; right:5px;'><img src='" + itv_globalWatermark + "' /></div>";
        }
    },

    pause: function () {
        this._player.vPause();
    },

    setWatermark: function (watermarkUrl, logoPosition) {
        this._player.vSetWatermark(watermarkUrl, VideoPlayer.LogoPosition.rightTop);
    },

    setCurrentTime: function (currentTime) {
        var player = document.getElementById(this._playerId);
        if (this._deviceType == this.DeviceType.pc)
            player.vSetCurrentTime(this.getTimestamp(currentTime));

        this._currentTimeOffset = parseInt(this.getTimestamp(currentTime) - new Date().getTime() / 1000);
    },

    setStartTime: function (startTime) {
        this.startTime = startTime;
        this._player.vSetStartTime(this.getTimestamp(this.startTime));
    },

    setEndTime: function (endTime) {
        this.endTime = endTime;
        this._player.vSetEndTime(this.getTimestamp(this.endTime));
    },

    getTimestamp: function (timeString, timezoneOffset) {
        var d = new Date();
        d.setUTCFullYear(parseInt(timeString.substr(0, 4), 10), parseInt(timeString.substr(5, 2) - 1, 10), parseInt(timeString.substr(8, 2), 10));
        d.setUTCHours(parseInt(timeString.substr(11, 2), 10), parseInt(timeString.substr(14, 2), 10), parseInt(timeString.substr(17, 2), 10), 0);

        var result = parseInt(d.getTime() / 1000);
        if (typeof timezoneOffset != "undefined") {
            result += -timezoneOffset * 60;
        }
        return result;
    },


    loaded: function () {
        if (this._deviceType == this.DeviceType.pc) {
            if (typeof itv_globalWatermark != "undefined" && itv_globalWatermark != "") {
                this._player.vSetWatermark(itv_globalWatermark, VideoPlayer.LogoPosition.rightTop);
            }
        }
        this.onLoaded();
    },
    onLoaded: function () { },

    completed: function () {
        this._prevBufferTime = 0;
        this._bufferCount = 0;
        this._bufferDurationTotal = 0;
        this._bufferDurationFirst = 0;

        this.onCompleted();
    },
    onCompleted: function () { }
}

VideoPlayer.prototype.init = function () {
    var container = document.getElementById(this._containerId);

    if (this._deviceType == this.DeviceType.iPad || this._deviceType == this.DeviceType.iPhone) {
        itv_videoPlayerLoaded();
    }
    else {
        var player = new SWFObject(itv_m3u8PlayerUrl, this._playerId, "100%", "100%", "10", "#000000");
        if (this._serverType == VideoPlayer.ServerType.so)
            player = new SWFObject(itv_soPlayerUrl, this._playerId, "100%", "100%", "10", "#000000");
        player.addParam("loop", "false");
        player.addParam("allowFullScreen", "true");
        player.addParam("scale", "noScale");
        player.addParam("wmode", "window");
        player.addParam("allowScriptAccess", "always");
        player.addVariable("width", container.style.width.replace("px", ""));
        player.addVariable("height", container.style.height.replace("px", ""));
        player.addVariable("serverType", this._serverType);
        player.addVariable("contentType", this._contentType);
        player.addVariable("playerSkin", itv_playerSkinUrl);
        player.addVariable("maxBufferLength", this.maxBufferLength);
        player.addVariable("localModel", eval(this._languageType));
        player.write(this._containerId);
    }
}

VideoPlayer.prototype.PlatformType =
{
    iPad: '2'
}

VideoPlayer.prototype.DeviceType =
{
    pc: '1',
    iPad: '2',
    iPhone: '3',
    aPad: '4'
}

VideoPlayer.ContentType =
{
    vod: '1',
    live: '2',
    virtual: "3"
}

VideoPlayer.ElementType =
{
    none: '-1',
    vod: '0',
    shift: '1',
    live: "2",
    ad: "3"
}

VideoPlayer.ServerType =
{
    so: 'so',
    cdn: 'cdn'
}

VideoPlayer.LogoPosition =
{
    leftTop: 'leftTop',
    rightTop: 'rightTop'
}

VideoPlayer.LanguageType =
{
    zh_cn: 'itv_videoplayer_local_zh_cn',
    en_us: 'itv_videoplayer_local_en_us',
    th_th: 'itv_videoplayer_local_th_th'
}

VideoPlayer.prototype.callLog = function (elementType, elementId) {
    var myContext = this;
    if (VideoPlayer.timer)
        clearTimeout(VideoPlayer.timer);

    var duration = this._prevDuration;
    if (this._prevPlayedTime > 0)
        duration += (new Date().getTime() - this._prevPlayedTime) / 1000;

    if (duration <= 0) {
        VideoPlayer.timer = setTimeout(function () { myContext.callLog.call(myContext); }, 1 * 1000);
        return;
    }
    duration = Math.ceil(duration);
    var ct = this._contentType;
    if (ct == VideoPlayer.ContentType.virtual) {
        ct = VideoPlayer.ContentType.live;
        if (this._elementType != VideoPlayer.ElementType.none)
            ct += this._elementType;
    }

    var logDiv = document.getElementById("itv_logDiv");
    if (logDiv == null) {
        logDiv = document.createElement('div');
        logDiv.id = "itv_logDiv";
        document.getElementById(this._containerId).appendChild(logDiv);
    }
    logDiv.innerHTML = "<img width='0px' height='0px' src='" + itv_logUrl + "?cid=" + this.programId + "&ct=" + ct + "&devicetype=" + this._deviceType + "&pt=3&bm=" + this.contentBM + "&sidx=" + this.seriesNumber + "&sid=" + this.seriesParentId + "&cn=" + this.channelNumber + "&d=" + duration + "&fb=" + Math.ceil(this._bufferDurationFirst) + "&tb=" + Math.ceil(this._bufferDurationTotal) + "&bt=" + this._bufferCount + "&at=0&key=" + this._key + "&random=" + Math.random().toString().replace(".", "") + "'></img>";

    VideoPlayer.timer = setTimeout(function () { myContext.callLog.call(myContext); }, 10000);
}

VideoPlayer.prototype.callLog2 = function () {
    var myContext = this;
    if (VideoPlayer.timer2)
        clearTimeout(VideoPlayer.timer2);

    var duration = this._prevDuration;
    if (this._prevPlayedTime > 0)
        duration += (new Date().getTime() - this._prevPlayedTime) / 1000;

    if (this._bufferCount == 0 || duration <= 0 || this._bufferDurationTotal > duration) {
        VideoPlayer.timer2 = setTimeout(function () { myContext.callLog2.call(myContext); }, 1 * 1000);
        return;
    }
    duration = Math.ceil(duration);

    var ct = this._contentType;
    if (ct == VideoPlayer.ContentType.virtual) {
        ct = VideoPlayer.ContentType.live;
        if (this._elementType != VideoPlayer.ElementType.none)
            ct += this._elementType;
    }

    var logDiv = document.getElementById("itv_logDiv2");
    if (logDiv == null) {
        logDiv = document.createElement('div');
        logDiv.id = "itv_logDiv2";
        document.getElementById(this._containerId).appendChild(logDiv);
    }
    logDiv.innerHTML = "<img width='0px' height='0px' src='" + itv_logUrl + "?cid=" + this.programId + "&ct=" + ct + "&devicetype=" + this._deviceType + "&pt=3&bm=" + this.contentBM + "&sidx=" + this.seriesNumber + "&sid=" + this.seriesParentId + "&cn=" + this.channelNumber + "&d=" + duration + "&fb=" + Math.ceil(this._bufferDurationFirst) + "&tb=" + Math.ceil(this._bufferDurationTotal) + "&bt=" + this._bufferCount + "&at=1&key=" + this._key + "&random=" + Math.random().toString().replace(".", "") + "'></img>";

    VideoPlayer.timer2 = setTimeout(function () { myContext.callLog2.call(myContext); }, 10 * 60 * 1000);
}

VideoPlayer.prototype.callLog3 = function (elementType, elementId) {
    if (typeof elementType != 'undefined' && elementType != VideoPlayer.ElementType.none && typeof elementId != 'undefined' && elementId != null && elementId != "") {
        var logDiv31 = document.getElementById("itv_logDiv31");
        var logDiv32 = document.getElementById("itv_logDiv32");

        if (logDiv31 == null) {
            logDiv31 = document.createElement('div');
            logDiv31.id = "itv_logDiv31";
            document.getElementById(this._containerId).appendChild(logDiv31);
        }
        if (logDiv32 == null) {
            logDiv32 = document.createElement('div');
            logDiv32.id = "itv_logDiv32";
            document.getElementById(this._containerId).appendChild(logDiv32);
        }

        var ct = this._contentType;
        if (ct == VideoPlayer.ContentType.virtual) {
            ct = VideoPlayer.ContentType.live;
            if (this._elementType != VideoPlayer.ElementType.none)
                ct += this._elementType;
        }

        logDiv31.innerHTML = "<img width='0px' height='0px' src='" + itv_logUrl + "?cid=" + this.elementId + "&ct=" + ct + "&devicetype=" + this._deviceType + "&pt=3&bm=" + this.contentBM + "&sidx=" + this.seriesNumber + "&sid=" + this.programId + "&cn=" + this.channelNumber + "&d=" + duration + "&fb=" + Math.ceil(this._bufferDurationFirst) + "&tb=" + Math.ceil(this._bufferDurationTotal) + "&bt=" + this._bufferCount + "&at=0&key=" + (this._key + "_" + 0) + "&random=" + Math.random().toString().replace(".", "") + "'></img>";
        if (this._elementType != VideoPlayer.ElementType.none) {
            logDiv32.innerHTML = "<img width='0px' height='0px' src='" + itv_logUrl + "?cid=" + this._elementId + "&ct=" + ct + "&devicetype=" + this._deviceType + "&pt=3&bm=" + this.contentBM + "&sidx=" + this.seriesNumber + "&sid=" + this.programId + "&cn=" + this.channelNumber + "&d=" + duration + "&fb=" + Math.ceil(this._bufferDurationFirst) + "&tb=" + Math.ceil(this._bufferDurationTotal) + "&bt=" + this._bufferCount + "&at=2&key=" + (this._key + "_" + 2) + "&random=" + Math.random().toString().replace(".", "") + "'></img>";
        }

        this._elementType = elementType;
        this._elementId = elementId;
    }
    else {
        this._elementType = VideoPlayer.ElementType.none;
        this._elementId = "";
    }
}

VideoPlayer.prototype.callLog4 = function () {
    var logDiv = document.getElementById("itv_logDiv4");
    if (logDiv == null) {
        logDiv = document.createElement('div');
        logDiv.id = "itv_logDiv4";
        document.getElementById(this._containerId).appendChild(logDiv);
    }

    var duration = this._prevDuration;
    if (this._prevPlayedTime > 0)
        duration += (new Date().getTime() - this._prevPlayedTime) / 1000;
    duration = Math.ceil(duration);

    var ct = this._contentType;
    if (ct == VideoPlayer.ContentType.virtual) {
        ct = VideoPlayer.ContentType.live;
        if (this._elementType != VideoPlayer.ElementType.none)
            ct += this._elementType;
    }

    logDiv.innerHTML = "<img width='0px' height='0px' src='" + itv_logUrl + "?cid=" + this.programId + "&ct=" + ct + "&devicetype=" + this._deviceType + "&pt=3&bm=" + this.contentBM + "&sidx=" + this.seriesNumber + "&sid=" + this.seriesParentId + "&cn=" + this.channelNumber + "&d=" + duration + "&fb=" + Math.ceil(this._bufferDurationFirst) + "&tb=" + Math.ceil(this._bufferDurationTotal) + "&bt=" + this._bufferCount + "&at=901&key=" + this._key + "&random=" + Math.random().toString().replace(".", "") + "'></img>";
}

VideoPlayer.prototype.callLogStatus = function () {
    var myContext = this;
    if (VideoPlayer.timerLogStatus)
        clearTimeout(VideoPlayer.timerLogStatus);

    var logDiv = document.getElementById("itv_logStatusDiv");
    if (logDiv == null) {
        logDiv = document.createElement('div');
        logDiv.id = "itv_logStatusDiv";
        document.getElementById(this._containerId).appendChild(logDiv);
    }

    var b = 0;
    if (VideoPlayer.prevBufferDurationTotal) {
        b = Math.ceil(itv_videoPlayerObject._bufferDurationTotal - VideoPlayer.prevBufferDurationTotal);
    }
    else {
        b = Math.ceil(itv_videoPlayerObject._bufferDurationTotal);
    }
    VideoPlayer.prevBufferDurationTotal = itv_videoPlayerObject._bufferDurationTotal;
    if (b < 0)
        b = 0;

    var bt = 0;
    if (VideoPlayer.prevBufferCount) {
        bt = itv_videoPlayerObject._bufferCount - VideoPlayer.prevBufferCount;
    }
    else {
        bt = itv_videoPlayerObject._bufferCount;
    }
    VideoPlayer.prevBufferCount = itv_videoPlayerObject._bufferCount;
    if (bt < 0) bt = 0

    var ds = 0;
    if (itv_videoPlayerObject._downloadCount > 0)
        ds = Math.ceil(itv_videoPlayerObject._downloadSpeedTotal / itv_videoPlayerObject._downloadCount * 8);

    if (ds > 4000)
        ds = 4000;

    logDiv.innerHTML = "<img width='0px' height='0px' src='" + itv_statusUrl + "?u=" + this.userToken + "&pt=3&b=" + b + "&bt=" + bt + "&ds=" + ds + "&us=0&p=0&scnid=0&pb=0&random=" + Math.random().toString().replace(".", "") + "'></img>";
    itv_videoPlayerObject._downloadSpeedTotal = 0;
    itv_videoPlayerObject._downloadCount = 0;

    VideoPlayer.timerLogStatus = setTimeout(function () { myContext.callLogStatus.call(myContext); }, 5 * 60 * 1000);
}

function itv_videoPlayerLoaded() {
    if (typeof itv_videoPlayerObject == "undefined") {
        setTimeout(itv_videoPlayerLoaded, 10);
        return;
    }
    itv_videoPlayerObject.loaded();
}

function itv_videoPlayCompleted() {
    if (itv_videoPlayerObject._contentType == VideoPlayer.ContentType.vod)
        itv_videoPlayerObject.completed();
}

function itv_videoPlayLiveProgramCompleted() {
    itv_videoPlayerObject.completed();
}

var itv_videoPlayErrorCompleted = false;
function itv_videoPlayError() {
    if (!itv_videoPlayErrorCompleted) {
        itv_videoPlayerObject.callLog4();
        itv_videoPlayErrorCompleted = true;
    }
}

function itv_videoPlayPlayed() {
    if (itv_videoPlayerObject._prevPlayedTime == 0) {
        itv_videoPlayerObject._prevPlayedTime = new Date().getTime();
        itv_videoPlayerObject._prevPausedTime = 0;
    }

    if (itv_videoPlayerObject._prevBufferTime > 0) {
        itv_videoPlayerObject._bufferCount += 1;
        itv_videoPlayerObject._bufferDurationTotal += (new Date().getTime() - itv_videoPlayerObject._prevBufferTime) / 1000;
        if (itv_videoPlayerObject._bufferCount == 1)
            itv_videoPlayerObject._bufferDurationFirst = itv_videoPlayerObject._bufferDurationTotal;
        itv_videoPlayerObject._prevBufferTime = 0;
    }
}

function itv_videoPlayPaused() {
    if (itv_videoPlayerObject._prevPausedTime == 0) {
        itv_videoPlayerObject._prevDuration += (new Date().getTime() - itv_videoPlayerObject._prevPlayedTime) / 1000;

        itv_videoPlayerObject._prevPausedTime = new Date().getTime();
        itv_videoPlayerObject._prevPlayedTime = 0;
    }
}

function itv_videoPlayBuffer() {
    if (itv_videoPlayerObject._prevBufferTime == 0) {
        itv_videoPlayerObject._prevBufferTime = new Date().getTime();
    }
}

function itv_videoCurrentTime(playedTime) {
    if (itv_videoPlayerObject._contentType == VideoPlayer.ContentType.live) {
        itv_videoPlayerObject.contentBM = 0;
        return;
    }

    if (itv_videoPlayerObject._deviceType == itv_videoPlayerObject.DeviceType.pc || itv_videoPlayerObject._deviceType == itv_videoPlayerObject.DeviceType.aPad) {
        if (!isNaN(playedTime))
            itv_videoPlayerObject.contentBM = parseInt(playedTime);
    }
    else if (itv_videoPlayerObject._deviceType == itv_videoPlayerObject.DeviceType.iPad || itv_videoPlayerObject._deviceType == itv_videoPlayerObject.DeviceType.iPhone) {
        var player = document.getElementById(itv_videoPlayerObject._playerId);
        if (!isNaN(player.currentTime))
            itv_videoPlayerObject.contentBM = parseInt(player.currentTime);
    }
}

function itv_videoReportDownloadSpeed(speed) {
    if (speed < 0)
        return;

    itv_videoPlayerObject._downloadSpeedTotal += speed;
    itv_videoPlayerObject._downloadCount++;
}
