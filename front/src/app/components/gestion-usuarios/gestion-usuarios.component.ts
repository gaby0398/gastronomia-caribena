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
    this.managerUserService.getManagerUser(this.userSearch).subscribe({
      next: (data) => {
        this.user.name = data.alias; // Carga el alias como nombre
        this.user.email = data.correo; // Carga el correo
        this.user.role = data.rol; // Carga el rol
        this.userLoaded = true;
        this.loading = false; // Desactiva el spinner
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
    console.log('Guardando cambios:', this.user);
    // Aquí iría la lógica para guardar los cambios usando el servicio correspondiente
  }

  closeErrorModal() {
    this.errorMessage = null; // Cierra el modal de error
  }
}