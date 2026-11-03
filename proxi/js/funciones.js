function salir()
{   
  Swal.fire({
      title: 'QUIERES SALIR DEL SISTEMA?',
      showDenyButton: true,
      showCancelButton: false,
      confirmButtonText: 'Si',
      denyButtonText: `No`,
    }).then((result) => {
      /* Read more about isConfirmed, isDenied below */
      if (result.isConfirmed) {
        //Swal.fire('Saved!', '', 'success')
      
        $.ajax({
          type: "POST",
          url: 'logout.php',
          error: function (XMLHttpRequest, textStatus, errorThrown)
          {
              let errorMsg = "<strong>Error al cargar el contenido:</strong><br>";
              if (XMLHttpRequest.status === 404)
              {
                  errorMsg += "Página no encontrada (404).";
              }
              else if(XMLHttpRequest.status === 500)
              {
                  errorMsg += "Error interno del servidor MYSQL (500).";
              }
              else
              {
                  errorMsg += "Error desconocido.<br>";
                  errorMsg += "Estado: " + textStatus + "<br>";
                  if(errorThrown)
                  {
                      errorMsg += "Mensaje: " + errorThrown;
                  };
              };
              console.error("AJAX error:", textStatus, errorThrown, XMLHttpRequest);
          },
          success: function (msj)
          {
              location.reload();
          },
      });

      } else if (result.isDenied) {
        Swal.fire('Changes are not saved', '', 'info')
      }
    })
};