import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';  // Importa CommonModule
import { RestauranteService } from '../../shared/services/restaurantes.service';
@Component({
  selector: 'app-restaurante-especifica',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './restaurante-especifica.component.html',
  styleUrl: './restaurante-especifica.component.css'
})
export class RestauranteEspecificaComponent {


  
  comidaEspecifica: any = {};  // Para almacenar los detalles de la comida

  constructor(private RestauranteService: RestauranteService, private router: Router) {}

  ngOnInit(): void {
    // Obtener el ID de la comida desde localStorage
    const idRestaurante = localStorage.getItem('idRestaurante');
    
    if (idRestaurante) {
      // Llamar al servicio para obtener la comida con el ID
      this.RestauranteService.getRestauranteId(Number(idRestaurante)).subscribe(
        (resp) => {
          if (resp && resp.data && resp.data.length > 0) {
            this.comidaEspecifica = resp.data[0];  // Asumiendo que el primer elemento es el correcto
          }
        },
        (error) => {
          console.error('Error al obtener los detalles de la comida:', error);
        }
      );
    }
  }



    // ESTO DEBE SER MODIFICADO POR UN ROUTERLINK CUANDO ESTE TODO EL PROYECTO ARMADO
    goBack(): void {
      localStorage.clear();
      this.router.navigate(['/restaurantes']); 
    }
     

}
