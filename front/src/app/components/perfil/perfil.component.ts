import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import { ManagerUserService } from '../../shared/services/manager-user.service';
import { AuthService } from '../../shared/services/auth.service';
import Swal from 'sweetalert2'; // Asegúrate de haber instalado SweetAlert2
import { TypeClient, User } from '../../shared/models/interface';

@Component({
  selector: 'app-perfil',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './perfil.component.html',
  styleUrls: ['./perfil.component.css']
})
export class PerfilComponent implements OnInit {
  private srvAuth = inject(AuthService);
  private managerUserService = inject(ManagerUserService);

  perfilForm: FormGroup;
  loading: boolean = false; // Para manejar el estado de carga

  constructor(private fb: FormBuilder) {
    // Inicializar el formulario con validaciones
    this.perfilForm = this.fb.group({
      nombre: ['', [Validators.required]],
      apellido1: ['', [Validators.required]],
      apellido2: ['', [Validators.required]],
      usuario: [{ value: '', disabled: true }, [Validators.required]], // Alias no editable
      correo: ['', [Validators.required, Validators.email]],
      password: ['', [Validators.minLength(6)]],
      confirmPassword: ['', [Validators.minLength(6)]],
      genero: ['', [Validators.required]]
    }, { validators: this.passwordMatchValidator });
  }

  ngOnInit(): void {
    // Obtener los datos del usuario actual
    const usuarioActual = this.srvAuth.valorUsrActual;

    if (usuarioActual) {
      // Obtener datos detallados del usuario desde ManagerUserService
      this.managerUserService.getUser(usuarioActual.alias).subscribe({
        next: (userData: TypeClient) => {
          this.perfilForm.patchValue({
            nombre: userData.nombre,
            apellido1: userData.apellido1,
            apellido2: userData.apellido2,
            usuario: userData.alias,
            correo: userData.correo,
            genero: userData.genero
          });
        },
        error: (error) => {
          console.error('Error al obtener los datos del usuario:', error);
          Swal.fire({
            title: 'Error',
            text: 'No se pudieron obtener los datos del perfil.',
            icon: 'error',
            confirmButtonText: 'Aceptar'
          });
        }
      });
    }
  }

  /**
   * Valida que las contraseñas coincidan
   */
  passwordMatchValidator(form: FormGroup) {
    const password = form.get('password')?.value;
    const confirmPassword = form.get('confirmPassword')?.value;
    if (password !== confirmPassword) {
      form.get('confirmPassword')?.setErrors({ mismatch: true });
    } else {
      form.get('confirmPassword')?.setErrors(null);
    }
    return null;
  }

  /**
   * Maneja la actualización del perfil
   */
  actualizarPerfil() {
    if (this.perfilForm.invalid) {
      Swal.fire({
        title: 'Error',
        text: 'Por favor, corrige los errores en el formulario.',
        icon: 'error',
        confirmButtonText: 'Aceptar'
      });
      return;
    }

    this.loading = true; // Iniciar indicador de carga

    // Obtener el alias del usuario actual
    const alias = this.srvAuth.valorUsrActual.alias;

    // Construir el objeto de actualización
    const datosActualizacion: Partial<TypeClient> = {
      nombre: this.perfilForm.get('nombre')?.value,
      apellido1: this.perfilForm.get('apellido1')?.value,
      apellido2: this.perfilForm.get('apellido2')?.value,
      correo: this.perfilForm.get('correo')?.value,
      genero: this.perfilForm.get('genero')?.value
    };

    // Si se ingresó una nueva contraseña, incluirla
    const password = this.perfilForm.get('password')?.value;
    if (password) {
      datosActualizacion.passw = password;
    }

    this.managerUserService.updateUser(alias, datosActualizacion).subscribe({
      next: (res) => {
        this.loading = false; // Finalizar indicador de carga
        Swal.fire({
          title: 'Éxito',
          text: 'Perfil actualizado correctamente.',
          icon: 'success',
          confirmButtonText: 'Aceptar'
        });
        // Opcional: refrescar los datos del usuario actual
        this.srvAuth.refreshAuth();
      },
      error: (error) => {
        this.loading = false; // Finalizar indicador de carga
        Swal.fire({
          title: 'Error',
          text: 'Hubo un error al actualizar el perfil. Por favor, intenta nuevamente más tarde.',
          icon: 'error',
          confirmButtonText: 'Aceptar'
        });
        console.error('Error al actualizar el perfil:', error);
      }
    });
  }
}