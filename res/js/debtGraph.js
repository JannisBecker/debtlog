function User(name, avatar, url) {
	this.userName = name;
	this.avatarImg = avatar;
	this.profileUrl = url;
	this.debtList = [];
}

function Debt(recipient, value) {
	this.recipient = recipient;
	this.value = value;
}

function Point(x,y) {
	this.x = x;
	this.y = y;
	this.data = function() { return "x:"+x+",y:"+y;};
}


var userlist = [],
	canvas = undefined,
	pxR = 0;

function getRotatedDistPoint(point, dist, angle) {
	var angle = angle * Math.PI / 180.0;
	return new Point(
		- Math.sin(angle) * dist + point.x,
		Math.cos(angle) * dist + point.y
	);
}

function init(users, c) {
	userlist = users;
	canvas = c;

	/* calc pixel ratio and scale context*/
	var ctx = c[0].getContext("2d"),
        dpr = window.devicePixelRatio || 1,
        bsr = ctx.webkitBackingStorePixelRatio ||
              ctx.mozBackingStorePixelRatio ||
              ctx.msBackingStorePixelRatio ||
              ctx.oBackingStorePixelRatio ||
              ctx.backingStorePixelRatio || 1;
    pxR = dpr / bsr;
}

function calcObjScale(min, ref) { //ref is at 1080px height
	return Math.max((canvas.height()*pxR)*ref/1080,min*pxR);
}

function buildGraph() {
	var canvasWidth = window.innerWidth,
		canvasHeight = Math.min(window.innerWidth,600);//window.innerHeight);

	canvas.removeLayers().drawLayers();
	canvas.attr("width", canvasWidth * pxR);
	canvas.attr("height", canvasHeight * pxR);

	canvas.css("width", canvasWidth);
	canvas.css("height", canvasHeight);

	var avatarRad = calcObjScale(0,100),
		circleScale = 0.8,
		circleRadius = ((canvas.height()*pxR-2*avatarRad)/2) * circleScale,
		circlePos = new Point((canvas.width()*pxR)/2, (canvas.height()*pxR)/2),
		circleUserDeg = 360 / userlist.length,
		curCircleDeg = 180;

	/*debug*/
	canvas.drawArc({
		layer: true,
		strokeStyle: '#252525',
		strokeWidth: calcObjScale(2,8),
		x: circlePos.x, y: circlePos.y,
		radius: circleRadius//+avatarRad-2
	});
	/*debugend*/

	/* Draw User Avatars */
	for(var userIndex in userlist) {
		var user = userlist[userIndex];
		var newPoint = getRotatedDistPoint(circlePos, circleRadius, curCircleDeg);

		/* Draw Avatar Image */
		canvas.addLayer({
			type: "arc",
			mask: true,
			x: newPoint.x, y: newPoint.y,
			radius: avatarRad,
		}).addLayer({
			type: "image",
			scale: avatarRad/100,
			source: user.avatarImg,
			x: newPoint.x, y: newPoint.y
		}).restoreCanvas({
			layer: true
		}).addLayer({
			type: "arc",
			strokeStyle: '#000',
			strokeWidth: calcObjScale(2,8),
			x: newPoint.x, y: newPoint.y,
			radius: avatarRad-1,
			data: {url: user.profileUrl},
			click: function(layer) {
				window.document.location += $(this).getLayer(layer).data.url;
			}
		});

		user.canvasObjPoint = newPoint;
		curCircleDeg += circleUserDeg;
	}

	/* Draw Debts and Arrows */
	for(var userIndex in userlist) {
		var user = userlist[userIndex];

		for(var debtIndex in user.debtList) {
			var debt = user.debtList[debtIndex];
			var userRecp = debt.recipient;
			var canvasObjPointRecp = userRecp.canvasObjPoint;

			/* Get Point where Arrow hits Recipient Avatar */
			var totalX = canvasObjPointRecp.x - user.canvasObjPoint.x,
				totalY = canvasObjPointRecp.y - user.canvasObjPoint.y,
				totalLength = Math.sqrt(totalX*totalX + totalY * totalY),
				endScale = (1 - ((avatarRad+4)/totalLength)),

				relX = totalX * endScale,
				relY = totalY * endScale,

				endX = relX + user.canvasObjPoint.x,
				endY = relY + user.canvasObjPoint.y;

			/* Get Point where Arrow starts */
			var	startScale = avatarRad/totalLength,
				relXStart = totalX * startScale,
				relYStart = totalY * startScale,
				startX = relXStart + user.canvasObjPoint.x,
				startY = relYStart + user.canvasObjPoint.y;

			/* Draw actual Arrow */
			canvas.addLayer({
				type: "line",
				index: debtIndex*2,
				strokeStyle: '#000',
				strokeWidth: calcObjScale(2,6), //2,8
				rounded: false,
				endArrow: true,
				arrowRadius: calcObjScale(5,50),
				arrowAngle: 30,
				x1: startX, y1: startY,
				x2: endX, y2: endY
			});

			/* Calc Debt Text position */
			var textArrowDistance = calcObjScale(2,25),
				textSize = calcObjScale(10,25),
				textVectorScale = textArrowDistance/totalLength,
				textVectorX = totalY * textVectorScale,
				textVectorY = totalX * textVectorScale,
				arrowAngle = 180*(Math.atan2(totalY,totalX))/Math.PI,
				absAngle = Math.abs(arrowAngle);

			/* Turn Text around if 90°<angle<270° */
			if(absAngle > 90 && absAngle < 270) {
				textVectorX *= -1;
				arrowAngle += 180;
			} else {
				textVectorY *= -1;
			}

			var textPosX = user.canvasObjPoint.x + (totalX/2) + textVectorX,
				textPosY = user.canvasObjPoint.y + (totalY/2) + textVectorY;

			/* debug
			canvas.addLayer({
				type: "line",
				strokeStyle: '#22a',
				strokeWidth: 4,
				rounded: true,
				arrowRadius: 44,
				arrowAngle: 22,
				x1: user.canvasObjPoint.x + (totalX/2), y1: user.canvasObjPoint.y + (totalY/2),
				x2: textPosX, y2: textPosY
			});
			/* debugend */


			/* Draw Debt Text */
			canvas.addLayer({
				type: "text",
				index: (debtIndex*2)+1,
				fillStyle: '#48e',
				x: textPosX, y: textPosY,
				rotate: arrowAngle,
				fontSize: textSize,
				fontStyle: "bold",
				fontFamily: 'Verdana, sans-serif',
				text: debt.value + " €"
				//text: arrowAngle
			});

		}
	}
	canvas.drawLayers();
}


