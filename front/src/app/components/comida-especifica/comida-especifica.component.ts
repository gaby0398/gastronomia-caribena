import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';  // Importa CommonModule
import { ComidasService } from '../../shared/services/comidas.service';
import { MatIconModule } from '@angular/material/icon';
import { RemarkComponent } from '../remark/remark.component';
import { MatDialog } from '@angular/material/dialog';
@Component({
  selector: 'app-comida-especifica',
  standalone: true,
  imports: [CommonModule,MatIconModule],
  templateUrl: './comida-especifica.component.html',
  styleUrl: './comida-especifica.component.scss'
})
export class ComidaEspecificaComponent {
  constructor(private comidasService: ComidasService, private router: Router, private dialog: MatDialog) { }

  comidaEspecifica: any = {};  // Para almacenar los detalles de la comida
  publicacionId: string = "Recetas-";

  openRemarkDialog(publicacionId: string): void {
    this.dialog.open(RemarkComponent, {
      data: { pageId: publicacionId },
      width: '600px',
      height: '400px',
    });
  }


  ngOnInit(): void {
    // Obtener el ID de la comida desde localStorage
    const idComida = localStorage.getItem('idComida');
    this.publicacionId += idComida ?? "Limbo";
    if (idComida) {
      // Llamar al servicio para obtener la comida con el ID
      this.comidasService.getComidaId(Number(idComida)).subscribe(
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
      this.router.navigate(['/comidas']); 
    }
     
}
