function slideRight(obj){
    obj.css("display", "block");
    obj.css("overflow", "hidden");
    obj.css("width", "0");
    obj.animate({
        width: "100%"
    }, 300);
}

function slideLeft(obj, callback){
    obj.css("display", "block");
    obj.css("overflow", "hidden");
    obj.animate({
        width: "0"
    }, 300, callback);
}

function resizeF(obj){
    $(obj).css("height", $(obj).contents().find("html").css("height"));
}

function switchh(cld){
    coobj = $($(".openobj", $(cld).closest(".olist"))[0]);
    //	ccld=0;
    //	while(coobj.length<1&&ccld<10){coobj=coobj.children(".openobj");ccld++;}
    oobj = coobj;
    
    //	$("img", oobj).each(
    //	function(n, obj){
    //	cwidth=obj.width;
    //	cheight=obj.height;
    //	if(!cheight){alert(1);}
    //	alert(cheight);
    //	}
    //	);
    
    if (oobj.is(".ars")) {
        $("*", oobj).each(function(n, obj){
            cwidth = obj.width;
            cheight = obj.height;
            obj = $(obj);
            pwidth = oobj.width();
            k = cheight / cwidth;
            if (cwidth > pwidth) {
                obj.width(pwidth);
                obj.height(k * pwidth);
            }
            else {
                obj.width(cwidth);
                obj.height(cheight);
            }
        });
    }
    //	alert(coobj[0]);
    if (oobj.is(":hidden") || oobj.width() == 0) {
        showtload(oobj);
        openbul(cld);
		if (!oobj.is(".topbox")) {
			co = $(".close", $(cld).parent());
			if (co.length > 0) {
				co = $(co[0]);
				if (co.is(":hidden")) {
					co.css("display", "table-cell");
					$(cld).hide();
				}
			}
		}
    }
    else {
        hidetunload(oobj);
        closebul(cld);
        co = $(".open", $(cld).parent());
        if (co.length > 0) {
            co = $(co[0]);
            if (co.is(":hidden")) {
                co.css("display", "table-cell");
                $(cld).hide();
                co.click()
            }
        }
    }
    
    //	$("img", oobj).each(
    //	function(n, obj){
    //	obj=$(obj);
    //	cheight=obj.height();
    //	obj.height(cheight+1);
    //	}
    //	);


}

function openbul(obj){
	obj=$(obj);
	if (obj.is(".dn") || obj.is(".up")) {
		obj.removeClass("dn up");
		obj.addClass("dn");
    }
	
	if(obj.html()=="+"||obj.html()=="–"){
		obj.html("&ndash;");
	}
}

function closebul(obj){
	obj=$(obj);
	if (obj.is(".dn")||obj.is(".up")) {
		obj.removeClass("dn up");
		obj.addClass("up");
    }
	
	if(obj.html()=="+"||obj.html()=="–"){
		obj.html("+");
	}
}



function sendntype(ar, obj){
    //lert($.serializeArray(ar));
    $.post("/func/newtype.php", ar, function(data){
        data = (data == "") ? "Ошибка" : data;
        co = $(".err", obj);
        co.html(data);
        co.slideDown("middle");
    });
}

function uptype(ar, obj){
    //lert($.serializeArray(ar));
    $.post("/func/typeedit.php", ar, function(data){
        data = (data == "") ? "Ошибка" : data;
        co = $(".err", obj);
        co.html(data);
        co.slideDown("middle");
    });
}


String.prototype.replaceAll = function(search, replace){
    return this.split(search).join(replace);
}


function init(obj){
    $("img", $(".PostContent", obj)).each(function(n, obj){
        width = obj.naturalWidth;
        if (width >= 700) {
            tmp = $("<div></div>");
            obj = $(obj);
            href = obj.attr("src");
            newa = $("<a class='lightbox' href='" + href + "'></a>");
            obj.replaceWith(tmp);
            newa.append(obj);
            tmp.replaceWith(newa);
            //obj.replaceWith(newa);
        }
    });
    if ($().lightbox) {
        $(".lightbox", obj).lightbox({
            fitToScreen: true,
            imageClickClose: false,
            resizeSpeed: 150,
        });
    }
    
    $(".voteup", obj).click(function(){
        cid = $(this).parent().parent().attr("cid");
        pid = $(this).parent().parent().attr("pid");
        if (pid) {
            vote(pid, 1, this);
        }
        if (cid) {
            votec(cid, 1, this);
        }
        
    });
    
    $(".votedn", obj).click(function(){
        cid = $(this).parent().parent().attr("cid");
        pid = $(this).parent().parent().attr("pid");
        if (pid) {
            vote(pid, 0, this);
        }
        if (cid) {
            votec(cid, 0, this);
        }
    });
    obj.onmousemove=function(){return false;}
    obj = $(obj);
    
    $(".edit", obj).click(function(){
    
        window.location = "?edit";
        
    });
    
    if (typeof sinit == 'function') {
        sinit(obj);
    }
    
    $(".newt", obj).submit(function(){
        //lert(111);
        sendntype($(this).serialize(), this);
        return false;
    });
    
    $(".typeed", obj).submit(function(){
        //lert(111);
        uptype($(this).serialize(), this);
        return false;
    });
    
    
    //	$("iframe", $(".content")).load(function(){
    //	resizeF(this);
    //	obj=this;
    //	$(this).contents().find("body").resize(function(){});
    //	});
    
    
    
    $("td", obj).dblclick(function(){
        return false;
    });
    
    $("div", obj).dblclick(function(){
        return false;
    });
    
    $(".open", $(".olist", obj)).each(function(n, obj){
    	obj=$(obj);
    	if(obj.attr("type")=="checkbox"){
    		obj.change(function(){
        		switchh(this);
        	});
    	}else{
    		obj.click(function(){
    			switchh(this);
    		});
    	}
    });
    
    $(".close", $(".olist", obj)).click(function(){
        switchh(this);
    });
	
	$(".topboxclose", obj).click(function(){
		$(".mw_topbox").remove();
		if(bodyscroll){
			$("body").css("overflow", bodyscroll);
		}else{
			$("body").css("overflow", "scroll");
		}
		if(loc){
			window.history.pushState(false, "test", loc);
		}else{
			window.history.pushState(false, "test", window.location.href.split("?")[0]);
		}
	});
    
    
    
    $(".switch", $(".swp", obj)).click(function(){
        coobj = $($(".sw", $(this).closest(".swp"))[0]);
        //		ccld=0;
        //		while(coobj.length<1&&ccld<10){coobj=coobj.children(".openobj");ccld++;}
        oobjs = $(coobj).parent().children(".sw");
        oobjs.each(function(n, obj){
            obj = $(obj);
            if (obj.is(":hidden") || obj.css("width") == "0px") {
                obj.css("width", "100%").css("display", "block");
                if ($(this).is(".dn") || $(this).is(".up")) {
                    $(this).removeClass("dn up");
                    $(this).addClass("dn");
                }
            }
            else {
                obj.css("display", "none");
                if ($(this).is(".dn") || $(this).is(".up")) {
                    $(this).removeClass("dn up");
                    $(this).addClass("up");
                }
            }
        })
    });
    
    
    $(".conf", obj).each(function(){
        this.addEventListener("change", cdm, false);
        this.addEventListener("keyup", cdm, false);
        function cdm(){
            ot = $(".cont", $(this).closest(".conp")[0])
            if (ot.is(".link")) {
                nval = s2link.translit(this.value);
            }
            else {
                nval = this.value;
            }
            ot.val(nval);
        }
    });
    
    $(".link", obj).keyup(function(){
        val = this.value;
        nval = s2link.translit(val);
        if (nval != val) {
            this.value = nval;
        }
    });
    
    $(".ttree", obj).children("div").show();
    
    $(".tobj", obj).change(function(){
        $(this).parent().children("div").hide();
        $(".t" + this.value, $(this).parent()[0]).slideDown("medium");
    });
    
    $("button.submit", obj).click(function(){
        log('submiting');
    	but = $(this);
        form = but.closest("form");
        if (!form.is(".async")) {
            return true;
        }
        but[0].disabled = true;
        req = form.serialize();
        if (but.attr("name")) {
            req = req + "&button_name=" + but.attr("name");
        }
        $.post(form.attr("action"), req, function(data){
            data = (data == "") ? "Ошибка" : data;
            form = $(but).closest("form");
            msg=$(".msg", form);
            if(!msg[0]){
	            co = $(".err", form);
	            co.html(data);
	            co.slideDown("middle");
            }else{
            	body=$(".body",msg);
            	dataar={};
            	dataar["msg"]="";
            	dataar=u2o(data);
            	if(!dataar["msg"]){
            		if(data==1){
	            		dataar["msg"]="Ok";
	            		dataar["stat"]="ok";
            		}else{
	            		dataar["msg"]=data;
	            		dataar["stat"]="err";
            		}
            		dataar["hide"]=1;
            	}
            	del0=msg.opacity>0?500:0;
            	
            	msg.stop().animate({ opacity: "0" }, del0, function(){
            		body.html(dataar["msg"]);
            		init(body);
                	body.removeClass("err ok");
                	body.addClass(dataar["stat"]);
                	del=Math.max(dataar["msg"].length*70, 200);
                	msg.css("opacity", "0").css("display", "block");
                	if(msg.css("opacity")=="0"){
                		msg.animate({ opacity: "1" }, 300, function(){
	                		if(dataar["hide"]==1){
	                			del=Math.max(dataar["msg"].length*70, 200);
	                			msg.delay(del).animate({opacity:"0"}, 300);
	                		}
                		});
                	}
            	});
            }
            but[0].disabled = false;
        });
        return false;
    });
    
    $(".req", obj).click(function(){
    	obj=$(this);
    	obj.unbind("click");
    	obj.click(function(){return false;});
    	$.get(obj.attr("href"), function(data){
            data = (data == "") ? "Ошибка" : data;
            obj.replaceWith($(data));
        });
    	
    	return false;
    });
    
    if (window.linit) {
        linit(obj);
    }
    else {
    }
}

function ihtml(obj){
    tmp = false;
    tmp = $("<div></div>");
    tmp.append(obj.clone());
    html = tmp.html();
    return html;
}
