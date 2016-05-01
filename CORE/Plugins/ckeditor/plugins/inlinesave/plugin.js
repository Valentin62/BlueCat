CKEDITOR.plugins.add( 'inlinesave',
{
	init: function( editor )
	{
		editor.addCommand( 'inlinesave',
			{
				exec : function( editor )
				{

					addData();

					function tempAlert(msg,duration)
					{
						var el = document.createElement("div");
						el.setAttribute("style","position:fixed;top:1%;width:100%;background-color:green;color: white;z-index: 1000;");
						el.innerHTML = msg;
						setTimeout(function(){
							el.parentNode.removeChild(el);
						},duration);
						document.body.appendChild(el);
					}
					
					function addData() {
						var data = editor.getData();
						var dataID = editor.container.getId();
						
						jQuery.ajax({

							type: "POST",

							//Specify the name of the file you wish to use to handle the data on your web page with this code:
							//<script>var dump_file="yourfile.php";</script>
							//(Replace "yourfile.php" with the relevant file you wish to use)
							//Data can be retrieved from the variable $_POST['editabledata']
							//The ID of the editor that the data came from can be retrieved from the variable $_POST['editorID']
							
							url: "../../../../Admin/Do/update_content.php",

							data: { datavalue: data, dataname: dataID }

						})

						.done(function (data, textStatus, jqXHR) {

							//alert(jqXHR.responseText);
							tempAlert("<center>Contenu enrengistré !</center>",2000);

						})

						.fail(function (jqXHR, textStatus, errorThrown) {

                            tempAlert("<center>ERREUR !</center>",2000);
							console.log('ERREUR: connexion impossible au fichier Admin/Do/update_content.php :(');

						});   
					} 

				}
			});
		editor.ui.addButton( 'Inlinesave',
		{
			label: 'Save',
			command: 'inlinesave',
			icon: this.path + 'images/inlinesave.png'
		} );
	}
} );