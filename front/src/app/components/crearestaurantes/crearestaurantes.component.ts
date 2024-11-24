import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { RouterModule, Router } from '@angular/router';
import { RestauranteService } from '../../shared/services/restaurantes.service';

@Component({
  selector: 'app-crearestaurantes',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule],
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

        this.router.navigate(['/restaurantes']);
       alert('Comida creada con éxito:');
  
      },
      (error) => {
        console.error('Error al crear el restaurante:', error);
      }
    );
  }

  goBack(): void {
    localStorage.clear();
    this.router.navigate(['/restaurantes']); 
  }
}
