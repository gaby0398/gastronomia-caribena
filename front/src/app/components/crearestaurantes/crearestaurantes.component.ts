import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { RestauranteService } from '../../shared/services/restaurantes.service';
import { MatIconModule } from '@angular/material/icon';
import Swal from 'sweetalert2';

@Component({
  selector: 'app-crearestaurantes',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule,MatIconModule],
  templateUrl: './crearestaurantes.component.html',
  styleUrls: ['./crearestaurantes.component.css']
})
export class CrearRestaurantesComponent {
  restaurante = {
    nombre_restaurante: '',
    descripcion_restaurante: '',
    direccion: '',
    imagen: '',
    usuario_id: '1' 
  };

  constructor(private restauranteService: RestauranteService, private router: Router) {}

  crearRestaurante(): void {
    this.restauranteService.crearRestaurante(this.restaurante).subscribe(
      (response) => {

        this.MensajeExito();
  
      },
      (error) => {
       this.MensajeError();
      }
    );
  }



  MensajeExito() {
    Swal.fire({
      title: '¡Creación completada!',
      text: 'La publicación se ha creado satisfactoriamente.',
      icon: 'success', 
      confirmButtonText: 'Entendido'
    }).then(() => {
      this.router.navigate(['/restaurantes']); 
    });
  }
  



  
  MensajeError() {
    Swal.fire({
      title: 'Ha ocurrido un error',
      text: 'Hemos tenido un problema para crear la publicación, vuelva a intentarlo.',
      icon: 'error', // Tipos: 'success', 'error', 'warning', 'info', 'question'
      confirmButtonText: 'Entendido'
    });
  }



  goBack(): void {
    localStorage.clear();
    this.router.navigate(['/restaurantes']); 
  }
}
