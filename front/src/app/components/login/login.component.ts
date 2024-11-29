import { Component, OnInit, inject } from '@angular/core';
import { CommonModule } from '@angular/common';
import { MatIconModule } from '@angular/material/icon';
import { RouterModule, Router } from '@angular/router';
import { AuthService } from '../../shared/services/auth.service';
import { FormBuilder, FormGroup, Validators, ReactiveFormsModule } from '@angular/forms';
import Swal from 'sweetalert2'; // Asegúrate de haber instalado SweetAlert2
import { RemarkComponent } from '../remark/remark.component';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule, MatIconModule, RouterModule, ReactiveFormsModule, RemarkComponent],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent implements OnInit {
  private srvAuth = inject(AuthService);
  private router = inject(Router);

  errorLogin: boolean = false;
  loginForm: FormGroup;
  loading: boolean = false; // Para manejar el estado de carga

  constructor(private fb: FormBuilder) {
    // Inicializar el formulario con validaciones
    this.loginForm = this.fb.group({
      usuario: ['', [Validators.required]],
      passw: ['', [Validators.required, Validators.minLength(6)]],
    });
  }

  ngOnInit(): void {
    // Cualquier lógica adicional al iniciar el componente
  }

  logear() {
    if (this.loginForm.invalid) {
      Swal.fire({
        title: 'Error',
        text: 'Por favor, completa todos los campos requeridos.',
        icon: 'error',
        confirmButtonText: 'Aceptar'
      });
      return;
    }

    this.loading = true; // Iniciar indicador de carga

    const credentials = this.loginForm.value;
    console.log(credentials);

    this.srvAuth.login(credentials).subscribe(
      (res: any) => {
        this.loading = false; // Finalizar indicador de carga
        if (res === true) {
          Swal.fire({
            title: 'Éxito',
            text: 'Inicio de sesión exitoso.',
            icon: 'success',
            confirmButtonText: 'Aceptar'
          }).then(() => {
            // Redireccionar al dashboard o página principal
            this.router.navigate(['']);
          });
        } else if (res === 401) {
          this.errorLogin = true;
          Swal.fire({
            title: 'Error',
            text: 'Credenciales inválidas. Por favor, verifica tu usuario y contraseña.',
            icon: 'error',
            confirmButtonText: 'Aceptar'
          });
        } else {
          // Manejar otros códigos de error si es necesario
          this.errorLogin = true;
          Swal.fire({
            title: 'Error',
            text: 'Hubo un error al iniciar sesión. Por favor, intenta nuevamente más tarde.',
            icon: 'error',
            confirmButtonText: 'Aceptar'
          });
        }
      },
      (error) => {
        this.loading = false; // Finalizar indicador de carga
        this.errorLogin = true;
        Swal.fire({
          title: 'Error',
          text: 'Hubo un error al iniciar sesión. Por favor, intenta nuevamente más tarde.',
          icon: 'error',
          confirmButtonText: 'Aceptar'
        });
        console.error('Error en el inicio de sesión:', error);
      }
    );
  }
}