import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { PlantasService } from '../../shared/services/plantas.service';
import { Router, RouterModule } from '@angular/router';

@Component({
  selector: 'app-crearplantas',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
  templateUrl: './crearplantas.component.html',
  styleUrls: ['./crearplantas.component.css']
})
export class CrearplantasComponent {
  publicacion = {
    nombre_planta: '',
    descripcion_planta: '', // Cambiado de `caracteristicas` a `descripcion_planta`
    imagen: '',
    usuario_id: 1, // Temporal hasta que la funcionalidad de usuarios esté activa.
    elaboracion: ''
  };

  constructor(private plantasService: PlantasService, private router: Router) {}

  /**
   * Envía los datos del formulario al servidor
   */
  crearPublicacion() {
    console.log('Nueva publicación:', this.publicacion);
    this.plantasService.crearPlanta(this.publicacion).subscribe(
      (response) => {
        this.router.navigate(['/plantas']);
        alert('Planta creada con éxito.');
      },
      (error) => {
        console.error('Error al crear la planta:', error);
        alert('Ha ocurrido un error. Inténtelo de nuevo.');
        this.formatearTexto();
      }
    );
  }

  /**
   * Limpia los campos del formulario
   */
  formatearTexto(): void {
    this.publicacion.nombre_planta = '';
    this.publicacion.descripcion_planta = ''; // Ajustado
    this.publicacion.elaboracion = '';
    this.publicacion.imagen = '';
  }

  /**
   * Regresa a la página anterior
   */
  goBack(): void {
    localStorage.clear();
    this.router.navigate(['/comidas']); 
  }
}
