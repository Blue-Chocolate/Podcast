// /api/user
// Returns current authenticated user details
type CurrentUserResponse = {
  success: boolean;
  data: {
    id: number;
    name: string;
    email: string;
    phone: string;
  };
};

// /api/login
// Login user with email and password
type LoginResponse = {
  success: boolean;
  token: string;
  data: {
    id: number;
    name: string;
    email: string;
    phone: string;
  };
};

// /api/register
// Register new user with name, email, phone and password
type RegisterResponse = {
  success: boolean;
  token: string;
  data: {
    id: number;
    name: string;
    email: string;
    phone: string;
  };
};
