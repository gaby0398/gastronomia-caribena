import { Component } from '@angular/core';
import { MatIconModule } from '@angular/material/icon';
import { Router, RouterModule } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms'; // Importar FormsModule
import { ManagerUserService } from '../../shared/services/manager-user.service';
import { TypeClient, Role } from '../../shared/models/interface';
import Swal from 'sweetalert2'; // Importar SweetAlert2

@Component({
  selector: 'app-crear-cuenta',
  standalone: true,
  imports: [MatIconModule, RouterModule, CommonModule, FormsModule],
  templateUrl: './crear-cuenta.component.html',
  styleUrls: ['./crear-cuenta.component.scss'],
})
export class CrearCuentaComponent {
  constructor(private managerUserService: ManagerUserService, private router: Router) { }

  // Modelo de datos para el formulario
  formData: any = {
    nombre: '',
    apellidos: '',
    alias: '',
    correo: '',
    password: '',
    confirmPassword: '',
    genero: ''
  };

  // Indica si se está procesando la creación de la cuenta
  loading: boolean = false;

  /**
   * Manejar la sumisión del formulario.
   * @param form Referencia al formulario.
   */
  onSubmit(form: any) {
    if (form.invalid) {
      Swal.fire({
        title: 'Error',
        text: 'Por favor, completa todos los campos requeridos.',
        icon: 'error',
        confirmButtonText: 'Aceptar'
      });
      return;
    }

    // Validar que las contraseñas coincidan
    if (this.formData.password !== this.formData.confirmPassword) {
      Swal.fire({
        title: 'Error',
        text: 'Las contraseñas no coinciden.',
        icon: 'error',
        confirmButtonText: 'Aceptar'
      });
      return;
    }

    // Validar el formato del correo electrónico
    if (!this.validateEmail(this.formData.correo)) {
      Swal.fire({
        title: 'Error',
        text: 'El formato del correo electrónico no es válido.',
        icon: 'error',
        confirmButtonText: 'Aceptar'
      });
      return;
    }

    // Validar que se hayan ingresado al menos dos apellidos
    if (!this.validateApellidos(this.formData.apellidos)) {
      Swal.fire({
        title: 'Error',
        text: 'Por favor, ingresa al menos dos apellidos separados por un espacio.',
        icon: 'error',
        confirmButtonText: 'Aceptar'
      });
      return;
    }

    // Evaluar la fortaleza de la contraseña
    const passwordStrength = this.evaluatePasswordStrength(this.formData.password);
    if (passwordStrength === 'Débil') {
      Swal.fire({
        title: 'Advertencia',
        text: 'La contraseña es débil. ¿Deseas continuar?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, continuar',
        cancelButtonText: 'Cancelar'
      }).then((result) => {
        if (result.isConfirmed) {
          this.createAccount();
        }
      });
      return;
    } else if (passwordStrength === 'Fuerte') {
      // Continuar sin advertencia
      this.createAccount();
      return;
    }

    // Si la contraseña es media, continuar sin advertencia
    this.createAccount();
  }

  /**
   * Crear la cuenta del usuario.
   */
  private createAccount() {
    this.loading = true;

    // Procesar los apellidos
    const apellidosArray = this.formData.apellidos.trim().split(' ');
    let apellido1 = '';
    let apellido2 = '';

    if (apellidosArray.length >= 2) {
      apellido1 = apellidosArray[0];
      apellido2 = apellidosArray.slice(1).join(' ');
    }

    // Preparar los datos para el servicio
    const userData: TypeClient = {
      alias: this.formData.alias,
      nombre: this.formData.nombre,
      apellido1: apellido1,
      apellido2: apellido2,
      telefono: 'N/A', // Puedes personalizar esto según tus necesidades
      celular: 'N/A',   // Puedes personalizar esto según tus necesidades
      correo: this.formData.correo,
      rol: Role.cliente, // Asignar un rol por defecto, por ejemplo 'Cliente'
      passw: this.formData.password,
      genero: this.formData.genero
    };

    console.log('Datos del usuario a enviar:', userData); // Verificar los datos

    // Llamar al servicio para crear el usuario
    this.managerUserService.createUser(userData).subscribe({
      next: (response) => {
        this.loading = false;
        Swal.fire({
          title: 'Éxito',
          text: 'Cuenta creada correctamente.',
          icon: 'success',
          confirmButtonText: 'Aceptar'
        }).then(() => {
          this.router.navigate(['/login']);
        });
      },
      error: (error) => {
        this.loading = false;
        if (error.status === 409) {
          Swal.fire({
            title: 'Error',
            text: 'El alias o correo ya existe.',
            icon: 'error',
            confirmButtonText: 'Aceptar'
          });
        } else if (error.status === 400 && error.error === "El campo 'apellido2' es requerido.") {
          Swal.fire({
            title: 'Error',
            text: 'El segundo apellido es requerido.',
            icon: 'error',
            confirmButtonText: 'Aceptar'
          });
        } else {
          Swal.fire({
            title: 'Error',
            text: 'Hubo un error al crear la cuenta intenta mas tarde O El Usuario o Correo ya existe.',
            icon: 'error',
            confirmButtonText: 'Aceptar'
          });
        }
      }
    });
  }

  /**
   * Validar el formato del correo electrónico.
   * @param email Correo electrónico a validar.
   * @returns Verdadero si el formato es válido, falso de lo contrario.
   */
  private validateEmail(email: string): boolean {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email.toLowerCase());
  }

  /**
   * Validar que el campo de apellidos contenga al menos dos palabras.
   * @param apellidos Cadena de apellidos a validar.
   * @returns Verdadero si contiene al menos dos palabras, falso de lo contrario.
   */
  private validateApellidos(apellidos: string): boolean {
    if (!apellidos) return false;
    const apellidosArray = apellidos.trim().split(' ');
    return apellidosArray.length >= 2;
  }

  /**
   * Evaluar la fortaleza de una contraseña.
   * @param password Contraseña a evaluar.
   * @returns 'Débil', 'Media' o 'Fuerte' según la evaluación.
   */
  private evaluatePasswordStrength(password: string): string {
    let strength = 0;

    if (password.length >= 6) strength++;
    if (password.match(/[A-Z]/)) strength++;
    if (password.match(/[0-9]/)) strength++;
    if (password.match(/[$@#&!]/)) strength++;

    if (strength <= 1) {
      return 'Débil';
    } else if (strength === 2 || strength === 3) {
      return 'Media';
    } else {
      return 'Fuerte';
    }
  }
}