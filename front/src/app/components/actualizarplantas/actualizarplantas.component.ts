import { Component, OnInit } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { Router, RouterModule } from '@angular/router';
import { PlantasService } from '../../shared/services/plantas.service';

@Component({
  selector: 'app-actualizarplantas',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './actualizarplantas.component.html',
  styleUrls: ['./actualizarplantas.component.scss']
})
export class ActualizarplantasComponent implements OnInit {
  publicacion = {
    nombre_planta: '',
    caracteristicas: '',
    imagen: '',
    elaboracion: ''
  };

  mostrarImagen: boolean = false;

  constructor(private plantasService: PlantasService, private router: Router) {}

  ngOnInit(): void {
    const idPlanta = localStorage.getItem('idPlanta');
    if (idPlanta) {
      this.getPlantaById(Number(idPlanta)); // Convertir correctamente el ID a número
    } else {
      console.error('No se encontró el ID de la planta en localStorage');
    }
  }

  getPlantaById(id: number): void {
    this.plantasService.getPlantaId(id).subscribe(
      (response) => {
        if (response && response.data && response.data.length > 0) {
          this.publicacion = response.data[0]; // Asignar directamente la primera planta
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
      const idPlanta = localStorage.getItem('idPlanta');
      if (idPlanta) {
        const payload = {
          nombre_planta: this.publicacion.nombre_planta,
          descripcion_planta: this.publicacion.caracteristicas, // Mapear aquí
          imagen: this.publicacion.imagen,
          elaboracion: this.publicacion.elaboracion,
        };
        this.plantasService.updatePlanta(parseInt(idPlanta), payload).subscribe(
          (response) => {
            alert('Cambios realizados con éxito');
            this.router.navigate(['/plantas']);
          },
          (error) => {
            console.error('Error al guardar los cambios:', error);
            alert('Ocurrió un error. Por favor, inténtelo de nuevo.');
          }
        );
      } else {
        console.error('Error inesperado: No se encontró el ID de la planta.');
      }
    } else {
      console.warn('Formulario inválido. No se pueden guardar los cambios.');
    }
  }

  goBack(): void {
    localStorage.clear();
    this.router.navigate(['/plantas']);
  }

  MuestraImagen(): void {
    const urlImagen = this.publicacion.imagen;
    if (urlImagen) {
      const width = 400;
      const height = 400;
      const left = (window.innerWidth / 2) - (width / 2);
      const top = (window.innerHeight / 2) - (height / 2);

      const ventana = window.open('', '_blank', `width=${width},height=${height},left=${left},top=${top}`);
      if (ventana) {
        ventana.document.write(`
          <html>
            <head>
              <title>Previsualización</title>
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
        alert('No se pudo abrir la ventana emergente.');
      }
    } else {
      alert('No hay URL de imagen disponible.');
    }
  }
}
