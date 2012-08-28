function Menu()
{	
	this.init();
}

Menu.prototype = 
{
	container: null,
	
	foregroundColor: null,
	backgroundColor: null,
	
	selector: null,
	save: null,
	exportImage: null,
	clear: null,
	about: null,
	
	init: function()
	{
		var option, space, separator, color_width = 48, color_height = 20;

		this.container = document.createElement("div");
		this.container.id = 'menu_bar';
		this.container.className = 'gui menu';
		//this.container.style.position = 'absolute';
		//this.container.style.top = '0px';
		
		var color_block = document.createElement("div");
		color_block.id = 'color_block';
		
		this.colorLabel = document.createElement("span");
		this.colorLabel.id = 'color_label';
		this.colorLabel.innerHTML = 'Color';
		color_block.appendChild(this.colorLabel);
		
		this.foregroundColor = document.createElement("canvas");
		this.foregroundColor.id = 'color_selector';
		//this.foregroundColor.style.marginBottom = '-6px';
		this.foregroundColor.style.cursor = 'pointer';
		//this.foregroundColor.width = color_width;
		//this.foregroundColor.height = color_height;
		this.foregroundColor.className = 'well';
		color_block.appendChild(this.foregroundColor);
		
		this.container.appendChild(color_block);
		
		this.setForegroundColor( COLOR );

		//space = document.createTextNode(" ");
		//this.container.appendChild(space);

		/* REMOVE BG SELECTOR
		this.backgroundColor = document.createElement("canvas");
		this.backgroundColor.style.marginBottom = '-6px';
		this.backgroundColor.style.marginRight = '30px';
		this.backgroundColor.style.cursor = 'pointer';
		this.backgroundColor.width = color_width;
		this.backgroundColor.height = color_height;
		this.backgroundColor.className = 'well';
		this.container.appendChild(this.backgroundColor);

		this.setBackgroundColor( BACKGROUND_COLOR );
		
		space = document.createTextNode(" ");
		this.container.appendChild(space);		*/
		
		// TODO add brush sizes here instead of styles
		
		//this.selector = document.createElement("select");
		//this.selector.style.marginRight = '10px';
		//this.selector.id = 'brush_selector';
		
		this.selector = [];
		var selector_container = document.createElement("span");
		selector_container.id = 'brush_selector';

		for (i = 0; i < BRUSHES.length; i++)
		{
		    
			//option = document.createElement("option");
			//option.id = i;
			//option.innerHTML = BRUSHES[i];
			//this.selector.appendChild(option);
			
			option = document.createElement("span");
			option.innerHTML = "<input type='radio' name='brush_selector' class='brush_selector' value='" + i + "'" + (i == 0 ? " checked" : "" )+ "><span>" + BRUSHES[i] + "</span>";
			selector_container.appendChild(option);
			this.selector.push(option);
			
		}

		//this.container.appendChild(this.selector);
		this.container.appendChild(selector_container);
		/*
		space = document.createTextNode(" ");
		this.container.appendChild(space);

		 
		this.save = document.createElement("span"); //getElementById('save');
		this.save.className = 'button';
		this.save.innerHTML = 'Save';
		this.container.appendChild(this.save);
		
		space = document.createTextNode(" | ");
		this.container.appendChild(space); */
		
		this.clear = document.createElement("Clear");
		this.clear.id = 'clear_button';
		this.clear.className = 'button';
		//this.clear.style.marginRight = '10px';
		this.container.appendChild(this.clear);
		
		this.exportImage = document.createElement("span"); //getElementById('exportImage');
		this.exportImage.id = 'done_button';
		this.exportImage.className = 'button';
		//this.exportImage.style.marginRight = '30px';
		this.container.appendChild(this.exportImage);

		/*
		this.about = document.createElement("About");
		this.about.className = 'button';
		this.about.innerHTML = 'About';
		this.container.appendChild(this.about);
		*/
	},
	
	setForegroundColor: function( color )
	{
		/*
		var context = this.foregroundColor.getContext("2d");
		context.fillStyle = 'rgb(' + color[0] + ', ' + color[1] +', ' + color[2] + ')';
		context.fillRect(0, 0, this.foregroundColor.width, this.foregroundColor.height);
		context.fillStyle = 'rgba(0, 0, 0, 0.1)';
		context.fillRect(0, 0, this.foregroundColor.width, 1);
		*/
		this.foregroundColor.style.backgroundColor = 'rgb(' + color[0] + ', ' + color[1] +', ' + color[2] + ')';
	},
	
	setBackgroundColor: function( color )
	{
		/*
		var context = this.backgroundColor.getContext("2d");
		context.fillStyle = 'rgb(' + color[0] + ', ' + color[1] +', ' + color[2] + ')';
		context.fillRect(0, 0, this.backgroundColor.width, this.backgroundColor.height);
		context.fillStyle = 'rgba(0, 0, 0, 0.1)';
		context.fillRect(0, 0, this.backgroundColor.width, 1);		
		*/
		this.backgroundColor.style.backgroundColor = 'rgb(' + color[0] + ', ' + color[1] +', ' + color[2] + ')';
	}
}
