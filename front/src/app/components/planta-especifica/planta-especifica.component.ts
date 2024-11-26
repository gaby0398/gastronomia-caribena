import { Component, CUSTOM_ELEMENTS_SCHEMA, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';  // Importa CommonModule
import { PlantasService } from '../../shared/services/plantas.service';
import { RemarkComponent } from '../remark/remark.component';

@Component({
  selector: 'app-planta-especifica',
  standalone: true,
  imports: [CommonModule, RemarkComponent],
  templateUrl: './planta-especifica.component.html', // Asegúrate de que este archivo existe
  styleUrls: ['./planta-especifica.component.css'],  // Corregido a styleUrls en plural
  schemas: [CUSTOM_ELEMENTS_SCHEMA]
})
export class PlantaEspecificaComponent implements OnInit {

  plantaEspecifica: any = {};  // Para almacenar los detalles de la planta
  id: any; // Indentificador para la caja de comentarios

  constructor(private plantasService: PlantasService, private router: Router) {}

  ngOnInit(): void {
    // Obtener el ID de la planta desde localStorage
    const idPlanta = localStorage.getItem('idPlanta');
    this.id = idPlanta;
    
    if (idPlanta) {
      // Llamar al servicio para obtener los detalles de la planta
      this.plantasService.getPlantaId(Number(idPlanta)).subscribe(
        (resp) => {
          if (resp && resp.data && resp.data.length > 0) {
            this.plantaEspecifica = resp.data[0];  // Asignar el primer elemento de la respuesta
          } else {
            console.warn('No se encontraron datos para esta planta.');
          }
        },
        (error) => {
          console.error('Error al obtener los detalles de la planta:', error);
        }
      );
    } else {
      console.error('ID de planta no encontrado en localStorage.');
    }
  }

  // Método para regresar a la lista de plantas
  goBack(): void {
    localStorage.clear();
    this.router.navigate(['/plantas']); 
  }
}
