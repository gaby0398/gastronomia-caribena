import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ManagerUserService } from '../../shared/services/manager-user.service';

@Component({
  selector: 'app-gestion-usuarios',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './gestion-usuarios.component.html',
  styleUrls: ['./gestion-usuarios.component.css']
})
export class GestionUsuariosComponent {
  private readonly managerUserService = inject(ManagerUserService);

  userSearch: string = ''; // Campo de búsqueda
  user = {
    name: '',
    email: '',
    role: 0
  }; // Información del usuario cargado

  initialRole: number = 0; // Almacena el rol inicial del usuario
  userLoaded = false; // Indica si los datos del usuario se han cargado
  loading = false; // Indicador de carga
  errorMessage: string | null = null; // Mensaje de error para el modal

  roles = [
    { value: 1, label: 'Administrador' },
    { value: 2, label: 'Supervisor' },
    { value: 4, label: 'Cliente' }
  ]; // Roles disponibles para el select

  goBack() {
    window.history.back();
  }

  searchUser() {
    if (!this.userSearch) {
      this.errorMessage = 'Por favor, ingrese un usuario para buscar.';
      return;
    }

    this.loading = true; // Activa el spinner
    // Llamada al servicio para obtener información del usuario
    this.managerUserService.getUser(this.userSearch).subscribe({
      next: (data) => {
        // Combinar nombre, apellido1 y apellido2 en el campo name
        this.user.name = `${data.nombre} ${data.apellido1} ${data.apellido2}`;
        this.user.email = data.correo; // Carga el correo
        this.user.role = data.rol; // Carga el rol
        this.initialRole = data.rol; // Almacena el rol inicial
        this.userLoaded = true; // Marca como cargado
        this.loading = false; // Desactivar el indicador de carga
      },
      error: (err) => {
        if (err === 404) {
          this.errorMessage = 'El usuario no existe.';
        } else {
          this.errorMessage = 'Hubo un error al conectarse al servidor.';
        }
        this.loading = false; // Desactiva el spinner
        this.userLoaded = false; // Marca como no cargado
      }
    });
  }

  saveChanges() {
    if (!this.userLoaded) {
      alert('No hay ningún usuario cargado para guardar cambios.');
      return;
    }

    // Verificar si el rol ha cambiado
    if (this.user.role !== this.initialRole) {
      this.managerUserService.changeRol(this.user.email, this.user.role).subscribe({
        next: () => {
          alert('El rol del usuario se actualizó correctamente.');
          this.initialRole = this.user.role; // Actualiza el rol inicial al nuevo
        },
        error: () => {
          alert('Ocurrió un error al actualizar el rol del usuario.');
        }
      });
    } else {
      alert('No se realizaron cambios en el rol.');
    }
  }

  closeErrorModal() {
    this.errorMessage = null; // Cierra el modal de error
  }
}