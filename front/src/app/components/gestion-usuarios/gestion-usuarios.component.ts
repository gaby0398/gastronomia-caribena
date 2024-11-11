import { Component } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-gestion-usuarios',
  standalone: true,
  imports: [CommonModule, FormsModule],
  templateUrl: './gestion-usuarios.component.html',
  styleUrls: ['./gestion-usuarios.component.css']
})
export class GestionUsuariosComponent {
  userSearch: string = '';
  user = {
    name: '',
    email: '',
    role: 'Usuario'
  };

  goBack() {
    window.history.back();
  }

  searchUser() {
    console.log('Buscando usuario:', this.userSearch);
  }

  saveChanges() {
    console.log('Guardando cambios:', {
      userName: this.user.name,
      userEmail: this.user.email,
      userRole: this.user.role
    });
  }
}
