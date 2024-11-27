// Importación de SweetAlert2
import { Component, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ManagerUserService } from '../../shared/services/manager-user.service';
import { TypeUser, Role, TypeClient } from '../../shared/models/interface';
import Swal from 'sweetalert2'; // Importar SweetAlert2

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
  user: {
    name: string;
    email: string;
    role: number;
  } = {
      name: '',
      email: '',
      role: 0
    }; // Información del usuario cargado

  initialRole: number = 0; // Almacena el rol inicial del usuario
  userLoaded = false; // Indica si los datos del usuario se han cargado
  loading = false; // Indicador de carga
  errorMessage: string | null = null; // Mensaje de error para el modal

  roles = [
    { value: Role.administrador, label: 'Administrador' },
    { value: Role.supervisor, label: 'Supervisor' },
    { value: Role.cliente, label: 'Cliente' }
  ]; // Roles disponibles para el select

  // Indica si un cambio de rol está en proceso
  isChangingRole: boolean = false;

  /**
   * Navegar hacia atrás en el historial del navegador.
   */
  goBack() {
    window.history.back();
  }

  /**
   * Buscar un usuario por alias o correo.
   */
  searchUser() {
    if (!this.userSearch.trim()) {
      this.errorMessage = 'Por favor, ingrese un usuario para buscar.';
      this.showErrorMessage('Validación de Entrada', this.errorMessage);
      return;
    }

    this.loading = true; // Activa el spinner
    this.errorMessage = null; // Resetear mensaje de error

    // Llamada al servicio para obtener información del usuario
    this.managerUserService.getUser(this.userSearch).subscribe({
      next: (data: TypeClient) => {
        // Combinar nombre, apellido1 y apellido2 en el campo name si existen
        if ('nombre' in data && 'apellido1' in data && 'apellido2' in data) {
          this.user.name = `${data.nombre} ${data.apellido1} ${data.apellido2}`;
        }
        this.user.email = data.correo; // Carga el correo
        this.user.role = data.rol; // Carga el rol
        this.initialRole = data.rol; // Almacena el rol inicial
        this.userLoaded = true; // Marca como cargado
        this.loading = false; // Desactivar el indicador de carga

        // Mostrar alerta de éxito al cargar el usuario
        Swal.fire({
          title: 'Éxito',
          text: 'Usuario encontrado y cargado correctamente.',
          icon: 'success',
          confirmButtonText: 'Aceptar'
        });
      },
      error: (err) => {
        if (err.status === 404) {
          this.errorMessage = 'El usuario no existe.';
          this.showErrorMessage('Error', this.errorMessage);
        } else {
          this.errorMessage = 'Hubo un error al conectarse al servidor.';
          this.showErrorMessage('Error', this.errorMessage);
        }
        this.loading = false; // Desactiva el spinner
        this.userLoaded = false; // Marca como no cargado
      }
    });
  }

  /**
   * Guardar los cambios realizados en el rol del usuario.
   */
  saveChanges() {
    if (!this.userLoaded) {
      Swal.fire({
        title: 'Advertencia',
        text: 'No hay ningún usuario cargado para guardar cambios.',
        icon: 'warning',
        confirmButtonText: 'Aceptar'
      });
      return;
    }

    // Verificar si el rol ha cambiado
    if (this.user.role !== this.initialRole) {
      if (this.isChangingRole) {
        // Si ya está en proceso de cambio, no hacer nada
        return;
      }

      this.isChangingRole = true; // Indica que el cambio está en proceso

      this.managerUserService.changeRol(this.user.email, this.user.role).subscribe({
        next: (result) => {
          if (result === true) {
            Swal.fire({
              title: 'Éxito',
              text: 'El rol del usuario se actualizó correctamente.',
              icon: 'success',
              confirmButtonText: 'Aceptar'
            });
            this.initialRole = this.user.role; // Actualiza el rol inicial al nuevo
          } else {
            Swal.fire({
              title: 'Error',
              text: 'Ocurrió un error al actualizar el rol del usuario.',
              icon: 'error',
              confirmButtonText: 'Aceptar'
            });
            // Revertir el cambio en el modelo
            this.user.role = this.initialRole;
          }
          this.isChangingRole = false; // Finaliza el proceso de cambio
        },
        error: () => {
          Swal.fire({
            title: 'Error',
            text: 'Ocurrió un error al actualizar el rol del usuario.',
            icon: 'error',
            confirmButtonText: 'Aceptar'
          });
          // Revertir el cambio en el modelo
          this.user.role = this.initialRole;
          this.isChangingRole = false; // Finaliza el proceso de cambio
        }
      });
    } else {
      Swal.fire({
        title: 'Información',
        text: 'No se realizaron cambios en el rol.',
        icon: 'info',
        confirmButtonText: 'Aceptar'
      });
    }
  }

  /**
   * Cerrar el modal de error.
   */
  closeErrorModal() {
    this.errorMessage = null; // Cierra el modal de error
  }

  /**
   * Obtener la etiqueta del rol basado en el valor numérico.
   * @param role Número que representa el rol.
   */
  getRoleLabel(role: number): string {
    const foundRole = this.roles.find(r => r.value === role);
    return foundRole ? foundRole.label : 'Desconocido';
  }

  /**
   * Mostrar mensajes de error utilizando SweetAlert2.
   * @param title Título de la alerta.
   * @param message Mensaje de la alerta.
   */
  private showErrorMessage(title: string, message: string) {
    Swal.fire({
      title: title,
      text: message,
      icon: 'error',
      confirmButtonText: 'Aceptar'
    });
  }
}