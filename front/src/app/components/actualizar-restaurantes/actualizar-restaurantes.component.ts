import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Router, RouterModule } from '@angular/router';
import { RestauranteService } from '../../shared/services/restaurantes.service';

@Component({
  selector: 'app-actualizar-restaurantes',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './actualizar-restaurantes.component.html',
  styleUrls: ['./actualizar-restaurantes.component.css']
})
export class ActualizarRestaurantesComponent implements OnInit {

  restaurante = {
    nombre_restaurante: '',
    descripcion_restaurante: '',
    direccion: '',
    imagen: ''
  };

  mostrarImagen: boolean = false;

  constructor(private restauranteService: RestauranteService, private router: Router) {}

  ngOnInit(): void {
    const idRestaurante = localStorage.getItem("idRestaurante");
      
    if(idRestaurante){
      this.getRestauranteById(Number(idRestaurante));
    }
    else{
      this.router.navigate(['/restaurantes']);
    }
  
      
    
  }

  getRestauranteById(id: number): void {
    this.restauranteService.getRestauranteId(id).subscribe(
      (response) => {
        if (response && response.data && response.data.length > 0) {
          this.restaurante = response.data[0]; // Asignar directamente la primera planta
        } else {
          console.error('No se encontraron datos para la planta con ID:', id);
        }
      },
      (error) => {
        console.error('Error al obtener los datos de la planta:', error);
      }
    );
  }

  guardarCambios(formulario: any): void {
    if (formulario.valid) {
      const idRestaurante = localStorage.getItem("idRestaurante");
      if (idRestaurante) {
        this.restauranteService.updateRestaurante(parseInt(idRestaurante), this.restaurante).subscribe(
          () => {
            alert('Cambios realizados con éxito');
            this.router.navigate(['/restaurantes']);
          },
          (error) => {
            alert('Error al actualizar el restaurante. Intenta nuevamente.');
            console.error(error);
          }
        );
      } else {
        alert('No se encontró el ID del restaurante.');
      }
    } else {
      alert('Formulario inválido. Revisa los datos.');
    }
  }

  goBack(): void {
    localStorage.clear();
    this.router.navigate(['/restaurantes']); 
  }


  MuestraImagen(): void {
    const urlImagen = this.restaurante.imagen;
  
    if (urlImagen) {
      // Calcular el centro de la pantalla
      const width = 400;  // Ancho de la ventana emergente
      const height = 400; // Alto de la ventana emergente
  
      const left = (window.innerWidth / 2) - (width / 2);
      const top = (window.innerHeight / 2) - (height / 2);
  
      // Abre la ventana emergente centrada
      const ventana = window.open('', '_blank', `width=${width},height=${height},left=${left},top=${top}`);
      
      if (ventana) {
        // Escribir contenido HTML dentro de la ventana emergente
        ventana.document.write(`
          <html>
            <head>
              <title>Previsualizacion de imagen de publicación</title>
              <style>
                body {
                  display: flex;
                  justify-content: center;
                  align-items: center;
                  height: 100%;
                  margin: 0;
                  background-color: #f5f5f5;
                }
                img {
                  max-width: 100%;
                  max-height: 100%;
                  display: block;
                }
              </style>
            </head>
            <body>
              <img src="${urlImagen}" alt="Imagen de referencia" />
            </body>
          </html>
        `);
      } else {
        alert('No se pudo abrir la ventana emergente. Asegúrate de que las ventanas emergentes estén permitidas.');
      }
    } else {
      alert('No hay URL de imagen disponible');
    }
  }
  
  
  
  



}
