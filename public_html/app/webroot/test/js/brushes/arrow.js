function arrow( context )
{
	this.init( context );
}

arrow.prototype =
{
	name: "Arrow",
	
	context: null,

	prevMouseX: null, prevMouseY: null,
	prevPrevX: null, prevPrevY: null,
	uX: null, uY: null,

	init: function( context )
	{
		this.context = context;
		this.context.globalCompositeOperation = 'source-over';
	},

	destroy: function()
	{
	},

	strokeStart: function( mouseX, mouseY )
	{
		this.prevMouseX = mouseX;
		this.prevMouseY = mouseY;
		this.prevPrevX = mouseX;
		this.prevPrevY = mouseY - 100;
		this.uX = 0; this.uY = 1;
	},

	stroke: function( mouseX, mouseY )
	{
		this.context.lineWidth = BRUSH_SIZE;	
		this.context.strokeStyle = "rgba(" + COLOR[0] + ", " + COLOR[1] + ", " + COLOR[2] + ", " + 0.5 * BRUSH_PRESSURE + ")";
		
		this.context.beginPath();
		this.context.moveTo(this.prevMouseX, this.prevMouseY);
		this.context.lineTo(mouseX, mouseY);
		this.context.stroke();

		this.prevPrevX = this.prevMouseX;
		this.prevPrevY = this.prevMouseY;
		
		this.prevMouseX = mouseX;
		this.prevMouseY = mouseY;
		
		// calculate unit vector on the fly, average by approx last 20px
		// use prevPrevX/Y
		// find unit vector
		newX = this.prevMouseX - this.prevPrevX;
		newY = this.prevMouseY - this.prevPrevY;
		
		uLen = Math.sqrt(Math.pow(newX, 2) + Math.pow(newY, 2));
		
		this.uX = ((this.prevMouseX - this.prevPrevX)) / uLen;
		this.uY = ((this.prevMouseY - this.prevPrevY)) / uLen;
		
		
		// TODO make arrowhead more reliable by adding a queue to track last 20px length worth of vectors
		
	},

	strokeEnd: function()
	{		
		x = this.uX;
		y = this.uY;
		
		// resultant unit vectors are rotated +135deg and -135deg
		u1x = x * Math.cos(2.355) - y * Math.sin(2.355);
		u1y = x * Math.sin(2.355) + y * Math.cos(2.355);
		u2x = x * Math.cos(-2.355) - y * Math.sin(-2.355);
		u2y = x * Math.sin(-2.355) + y * Math.cos(-2.355);
		
		u1x *= 20;
		u1y *= 20;
		u2x *= 20;
		u2y *= 20;
		
		// draw arrow
		this.context.lineWidth = BRUSH_SIZE;	
		this.context.strokeStyle = "rgba(" + COLOR[0] + ", " + COLOR[1] + ", " + COLOR[2] + ", " + 0.5 * BRUSH_PRESSURE + ")";
		
		this.context.beginPath();
		this.context.moveTo(this.prevMouseX, this.prevMouseY);
		this.context.lineTo(this.prevMouseX + u1x, this.prevMouseY + u1y);
		this.context.stroke();
		
		this.context.beginPath();
		this.context.moveTo(this.prevMouseX, this.prevMouseY);
		this.context.lineTo(this.prevMouseX + u2x, this.prevMouseY + u2y);
		this.context.stroke();
	}
}
