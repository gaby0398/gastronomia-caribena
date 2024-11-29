import { Component } from '@angular/core';
import { FormsModule } from '@angular/forms'; 
import { CommonModule } from '@angular/common'
import { ComidasService } from '../../shared/services/comidas.service';
import { Router, RouterModule } from '@angular/router';
import { MatIconModule } from '@angular/material/icon'; 
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import Swal from 'sweetalert2';




@Component({
  selector: 'app-crearcomidas',
  standalone: true,
  imports: [CommonModule, FormsModule, RouterModule, MatIconModule],
  templateUrl: './crearcomidas.component.html',
  styleUrl: './crearcomidas.component.scss'
})
export class CrearcomidasComponent {
 
  publicacion = {
    nombre_comida: '',
    descripcion_comida: '',
    imagen: '',
    usuario_id: 1,  // ESTATICO PORQUE AUN NO TENEMOS USUARIOS DE MANERA FUNCIONAL.
    elaboracion: ''
  };

  constructor(private comidasService: ComidasService, private router: Router) {}

  // Método para enviar los datos del formulario al servidor
  crearPublicacion() {
    console.log('Nueva publicación:', this.publicacion);
    this.comidasService.crearComida(this.publicacion).subscribe(
      (response) => {

        
        this.MensajeExito();
  
      },
      (error) => {
       this.MensajeError();
        this.FormateoTexto();
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
      this.router.navigate(['/comidas']); 
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








 
 
 // Borra texto del formulario para decir si se hizo la insercion o no
 FormateoTexto(): void{
  this.publicacion.nombre_comida = '';
  this.publicacion.descripcion_comida = '';
  this.publicacion.elaboracion = '';
  this.publicacion.imagen = '';
 }
 
 
 
  // debe cambiarse por routerlink cuando ya la app este consolidada. 
  goBack(): void {
      localStorage.clear();
      this.router.navigate(['/comidas']);  
  }

  
}
