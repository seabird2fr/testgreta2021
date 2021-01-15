var counter = $("#annonce_images .row").length;

			$("#add_image").click(function(){


					// recupère le prototype
					var tmpl = $("#annonce_images").data("prototype");
					//console.log(tmpl);

					tmpl = tmpl.replace(/__name__/g,counter++);
					//console.log(tmpl);

					$("#annonce_images").append(tmpl);

					deleteBlock();

				});


			function deleteBlock(){

				$('.del_image').click(function(){


					var id = $(this).data("bloc");
					//console.log(id);

					$('#'+id).remove();

					});

			}


			deleteBlock();